<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\DAO\Database;
use App\Core\Auth;
use Throwable;

class PdvController
{
    public function index(): void
    {
        try {
            Auth::requirePerfil(['admin', 'gerente', 'operador'], true);
            $contexto = $this->obterContextoAberto();

            $v = new \App\Core\View();
            $v->render('site/pdv/index', [
                'title'    => 'PDV',
                'pdvTurno' => $contexto,
            ]);
        } catch (\Throwable $e) {
            error_log('[PDV index] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
            // Durante a depuração, evite 500 cego:
            http_response_code(500);
            echo 'Erro interno no PDV (index).';
        }
    }

    public function pagamentos(): void
    {
        try {
            Auth::requirePerfil(['admin', 'gerente', 'operador'], true);
            $contexto = $this->obterContextoAberto();

            $v = new \App\Core\View();
            $v->render('site/pdv/pagamentos', [
                'title'    => 'PDV - Pagamentos',
                'pdvTurno' => $contexto,
            ]);
        } catch (\Throwable $e) {
            error_log('[PDV pagamentos] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            echo 'Erro interno no PDV (pagamentos).';
        }
    }

    /** GET /pdv/api/produtos?q=... */
    public function apiProdutos(): void
    {
        Auth::requirePerfil(['admin', 'gerente', 'operador'], true);
        header('Content-Type: application/json; charset=utf-8');

        $qRaw  = trim((string) ($_GET['q'] ?? ''));
        $limit = max(1, min(30, (int) ($_GET['limit'] ?? 10)));

        $pdo = Database::getConnection();

        $q        = $qRaw;
        $eq1      = $qRaw;
        $eq2      = $qRaw;
        $skuLike  = $qRaw . '%';
        $nomeLike = '%' . $qRaw . '%';
        $lim      = (int) $limit;

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
        $stmt->bindValue(':q', $q);
        $stmt->bindValue(':eq1', $eq1);
        $stmt->bindValue(':skuLike', $skuLike);
        $stmt->bindValue(':nomeLike', $nomeLike);
        $stmt->bindValue(':eq2', $eq2);
        $stmt->execute();

        echo json_encode($stmt->fetchAll(\PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
    }

    /** POST /pdv/api/venda  -> abre venda e cria meta PDV */
    public function apiCriarVenda(): void
    {
        Auth::requirePerfil(['admin', 'gerente', 'operador'], true);
        header('Content-Type: application/json; charset=utf-8');

        $data       = json_decode(file_get_contents('php://input'), true) ?? [];
        $clienteId  = (int) ($data['cliente_id'] ?? 1);
        $operadorId = (int) ($data['operador_id'] ?? 0);
        $terminalId = (int) ($data['terminal_id'] ?? 0);
        $turnoId    = (int) ($data['turno_id'] ?? 0);

        $contextoAtivo = $this->obterContextoAberto();
        if ($turnoId <= 0) {
            $turnoId = (int) ($contextoAtivo['turno_id'] ?? 0);
        }
        if ($terminalId <= 0) {
            $terminalId = (int) ($contextoAtivo['terminal_id'] ?? 0);
        }
        if ($operadorId <= 0) {
            $operadorId = (int) ($contextoAtivo['operador_id'] ?? 0);
        }

        if ($turnoId <= 0 || $terminalId <= 0 || $operadorId <= 0) {
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'error' => 'Nenhum turno/caixa aberto para registrar vendas.',
            ]);
            return;
        }

        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $pdo->prepare("
            INSERT INTO pedido (cliente_id, status, canal, entrega, pagamento, subtotal, frete, desconto, total, troco)
            VALUES (:c,'novo','pdv','retirada','na_entrega',0,0,0,0,0)
        ")->execute([':c' => $clienteId]);

            $pedidoId = (int) $pdo->lastInsertId();

            $pdo->prepare("
            INSERT INTO pdv_pedido_meta (pedido_id, terminal_id, turno_id, operador_id)
            VALUES (:p,:t,:u,:o)
        ")->execute([
                ':p' => $pedidoId,
                ':t' => $terminalId,
                ':u' => $turnoId,
                ':o' => $operadorId,
            ]);

            $pdo->commit();
            http_response_code(201);
            echo json_encode(['ok' => true, 'id' => $pedidoId], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }
    }

    /** POST /pdv/api/venda/{id}/pagamentos  body:{tipo,valor} */
    public function apiAdicionarPagamento($pedidoId): void
    {
        Auth::requirePerfil(['admin', 'gerente', 'operador'], true);
        header('Content-Type: application/json; charset=utf-8');
        $pedidoId = (int) $pedidoId;
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $tipo = (string) ($data['tipo'] ?? '');
        $valor = (float) ($data['valor'] ?? 0);

        if (!$pedidoId || !$tipo || $valor <= 0) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Dados inválidos']);
            return;
        }

        try {
            $pdo = Database::getConnection();
            $pdo->beginTransaction();

            $metaStmt = $pdo->prepare(
                'SELECT meta.turno_id, meta.terminal_id, meta.operador_id, turno.caixa_id
                   FROM pdv_pedido_meta meta
                   JOIN pdv_turno turno ON turno.id = meta.turno_id
                  WHERE meta.pedido_id = :p
                  LIMIT 1'
            );
            $metaStmt->execute([':p' => $pedidoId]);
            $meta = $metaStmt->fetch(\PDO::FETCH_ASSOC) ?: null;

            if (!$meta || empty($meta['caixa_id'])) {
                $pdo->rollBack();
                http_response_code(422);
                echo json_encode([
                    'ok' => false,
                    'error' => 'Nenhum turno/caixa aberto para este pedido.',
                ]);
                return;
            }

            $stmt = $pdo->prepare(
                'INSERT INTO pedido_pagamento (pedido_id, tipo, valor) VALUES (:p,:t,:v)'
            );
            $stmt->execute([':p' => $pedidoId, ':t' => $tipo, ':v' => $valor]);
            $pagamentoId = (int) $pdo->lastInsertId();

            $descricao = sprintf('Pagamento %s PDV #%d', ucfirst($tipo), $pedidoId);
            $movStmt = $pdo->prepare(
                'INSERT INTO mov_caixa (caixa_id, tipo, valor, descricao, pedido_id, terminal_id, turno_id)
                 VALUES (:caixa, :tipo, :valor, :descricao, :pedido, :terminal, :turno)'
            );
            $movStmt->execute([
                ':caixa' => (int) $meta['caixa_id'],
                ':tipo' => 'entrada',
                ':valor' => $valor,
                ':descricao' => $descricao,
                ':pedido' => $pedidoId,
                ':terminal' => (int) $meta['terminal_id'],
                ':turno' => (int) $meta['turno_id'],
            ]);

            $pdo->commit();
            echo json_encode(['ok' => true, 'pagamento_id' => $pagamentoId]);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }
    }

    /** POST /pdv/api/venda/{id}/finalizar  body:{itens:[{produto_id,quantidade,preco_unit}], desconto} */
    public function apiFinalizarVenda($pedidoId): void
    {
        Auth::requirePerfil(['admin', 'gerente', 'operador'], true);
        header('Content-Type: application/json; charset=utf-8');
        $pedidoId = (int) $pedidoId;
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $itens = $data['itens'] ?? [];
        $desconto = (float) ($data['desconto'] ?? 0);

        if (!$pedidoId || empty($itens)) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Sem itens']);
            return;
        }

        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $pdo->prepare('DELETE FROM item_pedido WHERE pedido_id=:p')->execute([':p' => $pedidoId]);

            $stmtItem = $pdo->prepare(
                'INSERT INTO item_pedido (pedido_id, produto_id, quantidade, preco_unit, desconto_unit)
                 VALUES (:p,:prod,:qtd,:preco,0)'
            );
            $stmtMov = $pdo->prepare(
                "INSERT INTO mov_estoque (produto_id, tipo, quantidade, origem, referencia_id, observacao)
                 VALUES (:prod,'saida',:qtd,'pedido',:pid,'Saida por venda')"
            );

            $subtotal = 0.0;
            foreach ($itens as $i) {
                $q = (float) $i['quantidade'];
                $pr = (float) $i['preco_unit'];
                $subtotal += $q * $pr;

                $stmtItem->execute([
                    ':p' => $pedidoId,
                    ':prod' => (int) $i['produto_id'],
                    ':qtd' => $q,
                    ':preco' => $pr,
                ]);

                $stmtMov->execute([
                    ':prod' => (int) $i['produto_id'],
                    ':qtd' => $q,
                    ':pid' => $pedidoId,
                ]);
            }

            $total = max(0, $subtotal - $desconto);

            $stmtPag = $pdo->prepare('SELECT COALESCE(SUM(valor),0) FROM pedido_pagamento WHERE pedido_id=:p');
            $stmtPag->execute([':p' => $pedidoId]);
            $totalPagamentos = (float) $stmtPag->fetchColumn();

            if ($totalPagamentos + 0.00001 < $total) {
                $pdo->rollBack();
                http_response_code(422);
                $faltante = max(0, $total - $totalPagamentos);
                echo json_encode([
                    'ok' => false,
                    'error' => 'Pagamentos insuficientes',
                    'faltante' => $faltante,
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $troco = max(0, $totalPagamentos - $total);

            $stmt = $pdo->prepare(
                "UPDATE pedido
                    SET subtotal=:sub,
                        desconto=:desc,
                        total=:tot,
                        troco=:troco,
                        status='finalizado'
                  WHERE id=:id"
            );
            $stmt->execute([
                ':sub' => $subtotal,
                ':desc' => $desconto,
                ':tot' => $total,
                ':troco' => $troco,
                ':id' => $pedidoId,
            ]);

            $pdo->commit();
            echo json_encode([
                'ok' => true,
                'pedido_id' => $pedidoId,
                'subtotal' => $subtotal,
                'total' => $total,
                'pagamentos' => $totalPagamentos,
                'troco' => $troco,
            ], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }
    }

    public function apiCancelarVendaVazia(int $pedidoId): void
    {
        Auth::requirePerfil(['admin', 'gerente', 'operador'], true);
        header('Content-Type: application/json; charset=utf-8');
        $pdo = Database::getConnection();

        try {
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

            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM pdv_pedido_meta WHERE pedido_id=:p")->execute([':p' => $pedidoId]);
            $pdo->prepare("DELETE FROM pedido WHERE id=:p")->execute([':p' => $pedidoId]);
            $pdo->commit();

            echo json_encode(['ok' => true]);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }
    }

    // public function abrirTurno(): void
    // {
    //     \App\Core\Auth::requirePerfil(['admin', 'gerente'], true);
    //     header('Content-Type: application/json; charset=utf-8');

    //     $data = json_decode(file_get_contents('php://input'), true) ?? [];
    //     $terminalId = (int)($data['terminal_id'] ?? 1);
    //     $operadorId = (int)($data['operador_id'] ?? 1);
    //     $troco      = (float)($data['troco'] ?? 100.00);

    //     $pdo = \App\DAO\Database::getConnection();
    //     $pdo->beginTransaction();
    //     try {
    //         // garante terminal
    //         $pdo->prepare("INSERT INTO pdv_terminal (id,nome)
    //                    VALUES (:id,'Caixa 01')
    //                    ON DUPLICATE KEY UPDATE nome=VALUES(nome)")
    //             ->execute([':id' => $terminalId]);

    //         // cria caixa aberto
    //         $pdo->prepare("INSERT INTO caixa (nome, saldo_inicial, status, aberto_em)
    //                    VALUES ('Caixa Loja', :troco, 'aberto', NOW())")
    //             ->execute([':troco' => $troco]);
    //         $caixaId = (int)$pdo->lastInsertId();

    //         // abre turno
    //         $pdo->prepare("INSERT INTO pdv_turno (caixa_id, terminal_id, operador_id, status, aberto_em)
    //                    VALUES (:c,:t,:o,'aberto', NOW())")
    //             ->execute([':c' => $caixaId, ':t' => $terminalId, ':o' => $operadorId]);

    //         $pdo->commit();
    //         echo json_encode(['ok' => true, 'caixa_id' => $caixaId], JSON_UNESCAPED_UNICODE);
    //     } catch (\Throwable $e) {
    //         if ($pdo->inTransaction()) $pdo->rollBack();
    //         http_response_code(500);
    //         echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    //     }
    // }

    public function abrirTurno(): void
    {
        \App\Core\Auth::requirePerfil(['admin', 'gerente', 'operador'], true);
        header('Content-Type: application/json; charset=utf-8');

        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $terminalId = (int)($data['terminal_id'] ?? 1);
        $operadorId = (int)($data['operador_id'] ?? 0);
        $troco      = (float)($data['troco'] ?? 100.00);

        // fallback: se não veio no payload, usa usuário logado
        if ($operadorId <= 0) {
            $user = \App\Core\Auth::user() ?? [];
            $operadorId = (int)($user['id'] ?? 0);
        }

        if ($operadorId <= 0) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'Operador inválido.']);
            return;
        }

        $pdo = \App\DAO\Database::getConnection();
        $pdo->beginTransaction();
        try {
            // garante terminal
            $pdo->prepare("
            INSERT INTO pdv_terminal (id, nome)
            VALUES (:id, 'Caixa 01')
            ON DUPLICATE KEY UPDATE nome = VALUES(nome)
        ")->execute([':id' => $terminalId]);

            // >>> INSERE caixa com operador_id e terminal_id <<<
            $pdo->prepare("
            INSERT INTO caixa (nome, terminal_id, operador_id, saldo_inicial, status, aberto_em)
            VALUES ('Caixa Loja', :terminal, :operador, :troco, 'aberto', NOW())
        ")->execute([
                ':terminal' => $terminalId,
                ':operador' => $operadorId,
                ':troco'    => $troco,
            ]);
            $caixaId = (int)$pdo->lastInsertId();

            // abre turno
            $pdo->prepare("
            INSERT INTO pdv_turno (caixa_id, terminal_id, operador_id, status, aberto_em)
            VALUES (:c, :t, :o, 'aberto', NOW())
        ")->execute([
                ':c' => $caixaId,
                ':t' => $terminalId,
                ':o' => $operadorId,
            ]);

            $pdo->commit();
            echo json_encode(['ok' => true, 'caixa_id' => $caixaId], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }
    }



    public function fecharTurno(): void
    {
        \App\Core\Auth::requirePerfil(['admin', 'gerente'], true);
        header('Content-Type: application/json; charset=utf-8');

        $pdo = \App\DAO\Database::getConnection();
        try {
            // pega último turno aberto
            $turno = $pdo->query("SELECT id, caixa_id FROM pdv_turno
                              WHERE status='aberto'
                              ORDER BY aberto_em DESC LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
            if (!$turno) {
                echo json_encode(['ok' => true, 'msg' => 'já fechado']);
                return;
            }

            $pdo->beginTransaction();
            $pdo->prepare("UPDATE pdv_turno SET status='fechado', fechado_em=NOW() WHERE id=:id")
                ->execute([':id' => $turno['id']]);
            // opcional: marcar caixa como fechado
            $pdo->prepare("UPDATE caixa SET status='fechado', fechado_em=NOW() WHERE id=:c")
                ->execute([':c' => $turno['caixa_id']]);
            $pdo->commit();

            echo json_encode(['ok' => true]);
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }
    }


    private function obterContextoAberto(): array
    {
        try {
            $pdo = Database::getConnection();
            $sql = "
            SELECT t.id AS turno_id,
                   t.caixa_id,
                   t.terminal_id,
                   t.operador_id,
                   term.nome AS terminal_nome
              FROM pdv_turno t
              JOIN pdv_terminal term ON term.id = t.terminal_id
             WHERE t.status = 'aberto'
             ORDER BY t.aberto_em DESC
             LIMIT 1
        ";
            $stmt  = $pdo->query($sql);
            $turno = $stmt ? $stmt->fetch(\PDO::FETCH_ASSOC) : false;
            return is_array($turno) ? $turno : [];
        } catch (\PDOException $e) {
            // Se for "tabela não existe" (42S02), apenas retorna contexto vazio
            if ($e->getCode() === '42S02') {
                error_log('[PDV contexto] Tabela não encontrada: ' . $e->getMessage());
                return [];
            }
            // Outros erros de DB sobem para serem logados no chamador
            throw $e;
        }
    }
}
