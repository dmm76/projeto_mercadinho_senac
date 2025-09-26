<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\DAO\Database;
use Throwable;

class PdvController
{
    public function index(): void
    {
        $v = new \App\Core\View();
        $v->render('site/pdv/index', ['title' => 'PDV']);
    }

    /** GET /pdv/api/produtos?q=... */
    public function apiProdutos(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $qRaw  = trim((string)($_GET['q'] ?? ''));
        $limit = max(1, min(30, (int)($_GET['limit'] ?? 10)));

        $pdo = \App\DAO\Database::getConnection();

        $q        = $qRaw;
        $eq1      = $qRaw;            // para WHERE
        $eq2      = $qRaw;            // para ORDER BY
        $skuLike  = $qRaw . '%';
        $nomeLike = '%' . $qRaw . '%';
        $lim      = (int)$limit;

        $sql = "
      SELECT
        p.id, p.nome, p.sku, p.ean,
        pr.preco_venda AS preco_venda,
        COALESCE(pr.preco_promocional, pr.preco_venda) AS preco_corrente,
        COALESCE(e.quantidade,0) AS estoque
      FROM produto p
      JOIN preco pr        ON pr.produto_id = p.id
      LEFT JOIN estoque e  ON e.produto_id = p.id
      WHERE p.ativo = 1
        AND (
              :q = ''
           OR  p.ean = :eq1
           OR  p.sku LIKE :skuLike
           OR  p.nome LIKE :nomeLike
        )
      ORDER BY (p.ean = :eq2) DESC, p.nome ASC
      LIMIT {$lim}
    ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':q',        $q);
        $stmt->bindValue(':eq1',      $eq1);
        $stmt->bindValue(':skuLike',  $skuLike);
        $stmt->bindValue(':nomeLike', $nomeLike);
        $stmt->bindValue(':eq2',      $eq2);
        $stmt->execute();

        echo json_encode($stmt->fetchAll(\PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
    }



    /** POST /pdv/api/venda  -> abre venda e cria meta PDV */
    public function apiCriarVenda(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $data       = json_decode(file_get_contents('php://input'), true) ?? [];
        $clienteId  = (int)($data['cliente_id']  ?? 1);
        $operadorId = (int)($data['operador_id'] ?? 1);
        $terminalId = (int)($data['terminal_id'] ?? 1);
        $turnoId    = (int)($data['turno_id']    ?? 1);

        $pdo = \App\DAO\Database::getConnection();
        $pdo->beginTransaction();

        try {
            // cria pedido PDV básico
            $pdo->prepare("
            INSERT INTO pedido (cliente_id, status, canal, entrega, pagamento, subtotal, frete, desconto, total, troco)
            VALUES (:c,'novo','pdv','retirada','na_entrega',0,0,0,0,0)
        ")->execute([':c' => $clienteId]);

            $pedidoId = (int)$pdo->lastInsertId();

            // meta PDV (necessária para a trigger do caixa)
            $pdo->prepare("
            INSERT INTO pdv_pedido_meta (pedido_id, terminal_id, turno_id, operador_id)
            VALUES (:p,:t,:u,:o)
        ")->execute([
                ':p' => $pedidoId,
                ':t' => $terminalId,
                ':u' => $turnoId,
                ':o' => $operadorId
            ]);

            $pdo->commit();
            http_response_code(201);
            echo json_encode(['ok' => true, 'id' => $pedidoId], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }
    }


    /** POST /pdv/api/venda/{id}/pagamentos  body:{tipo,valor} */
    public function apiAdicionarPagamento($pedidoId): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $pedidoId = (int)$pedidoId;
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $tipo = (string)($data['tipo'] ?? '');
        $valor = (float)($data['valor'] ?? 0);

        if (!$pedidoId || !$tipo || $valor <= 0) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Dados inválidos']);
            return;
        }

        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
              INSERT INTO pedido_pagamento (pedido_id, tipo, valor) VALUES (:p,:t,:v)
            ");
            $stmt->execute([':p' => $pedidoId, ':t' => $tipo, ':v' => $valor]);
            // A trigger trg_pagamento_insere_movcaixa fará o lançamento em mov_caixa
            echo json_encode(['ok' => true]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }
    }

    /** POST /pdv/api/venda/{id}/finalizar  body:{itens:[{produto_id,quantidade,preco_unit}], desconto} */
    public function apiFinalizarVenda($pedidoId): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $pedidoId = (int)$pedidoId;
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $itens = $data['itens'] ?? [];
        $desconto = (float)($data['desconto'] ?? 0);

        if (!$pedidoId || empty($itens)) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Sem itens']);
            return;
        }

        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            // limpa itens existentes (se reabrir e reenviar)
            $pdo->prepare("DELETE FROM item_pedido WHERE pedido_id=:p")->execute([':p' => $pedidoId]);

            // itens + movimentação de estoque
            $stmtItem = $pdo->prepare("
              INSERT INTO item_pedido (pedido_id, produto_id, quantidade, preco_unit, desconto_unit)
              VALUES (:p,:prod,:qtd,:preco,0)
            ");
            $stmtMov = $pdo->prepare("
              INSERT INTO mov_estoque (produto_id, tipo, quantidade, origem, referencia_id, observacao)
              VALUES (:prod,'saida',:qtd,'pedido',:pid,'Saida por venda')
            ");

            $subtotal = 0.0;
            foreach ($itens as $i) {
                $q = (float)$i['quantidade'];
                $pr = (float)$i['preco_unit'];
                $subtotal += $q * $pr;

                $stmtItem->execute([
                    ':p' => $pedidoId,
                    ':prod' => (int)$i['produto_id'],
                    ':qtd' => $q,
                    ':preco' => $pr
                ]);

                $stmtMov->execute([
                    ':prod' => (int)$i['produto_id'],
                    ':qtd' => $q,
                    ':pid' => $pedidoId
                ]);
            }

            $total = max(0, $subtotal - $desconto);

            // atualiza totais e marca finalizado
            $stmt = $pdo->prepare("
              UPDATE pedido
                 SET subtotal=:sub, desconto=:desc, total=:tot, status='finalizado'
               WHERE id=:id
            ");
            $stmt->execute([
                ':sub' => $subtotal,
                ':desc' => $desconto,
                ':tot' => $total,
                ':id' => $pedidoId
            ]);

            $pdo->commit();
            echo json_encode(['ok' => true, 'pedido_id' => $pedidoId, 'subtotal' => $subtotal, 'total' => $total]);
        } catch (Throwable $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }
    }

    public function apiCancelarVendaVazia(int $pedidoId): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $pdo = Database::getConnection();

        try {
            // só deleta se estiver "novo" e sem itens/pagamentos
            $sql = "
          SELECT p.id
            FROM pedido p
            LEFT JOIN item_pedido ip ON ip.pedido_id=p.id
            LEFT JOIN pedido_pagamento pp ON pp.pedido_id=p.id
           WHERE p.id=:id AND p.canal='pdv' AND p.status='novo'
           GROUP BY p.id
          HAVING COUNT(ip.id)=0 AND COUNT(pp.id)=0
        ";
            $st = $pdo->prepare($sql);
            $st->execute([':id' => $pedidoId]);
            if (!$st->fetchColumn()) {
                http_response_code(422);
                echo json_encode(['ok' => false, 'error' => 'Pedido possui dados ou não está em estado "novo"']);
                return;
            }

            // apaga meta e pedido
            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM pdv_pedido_meta WHERE pedido_id=:p")->execute([':p' => $pedidoId]);
            $pdo->prepare("DELETE FROM pedido WHERE id=:p")->execute([':p' => $pedidoId]);
            $pdo->commit();

            echo json_encode(['ok' => true]);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }
    }
}
