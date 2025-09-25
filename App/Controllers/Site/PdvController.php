<?php
declare(strict_types=1);

namespace App\Controllers\Site;

use App\Core\View;
use App\DAO\Database;
use Throwable;

class PdvController
{
    public function index(): void
{
    $view = new \App\Core\View();
    $view->render('site/pdv/index', [
        'title' => 'PDV', // opcional, se seu layout usar
    ]);
}


    /**
     * GET /pdv/api/produtos?q=arroz&limit=10
     * Retorna: [{id,nome,sku,preco,estoque}]
     */
    public function apiProdutos(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $q     = trim((string)($_GET['q'] ?? ''));
        $limit = max(1, min(50, (int)($_GET['limit'] ?? 10)));

        $pdo = Database::getConnection();

        // Busca por nome/sku, com preço atual (tabela preco) e estoque (tabela estoque)
        $sql = "
            SELECT p.id, p.nome, p.sku,
                   pr.preco_venda AS preco,
                   COALESCE(e.quantidade, 0) AS estoque
            FROM produto p
            JOIN preco pr ON pr.produto_id = p.id
            LEFT JOIN estoque e ON e.produto_id = p.id
            WHERE p.ativo = 1
              AND pr.inicio_promo IS NULL AND pr.fim_promo IS NULL
              AND (:q = '' OR p.nome LIKE :like OR p.sku LIKE :like)
            ORDER BY p.nome
            LIMIT :lim
        ";

        $stmt = $pdo->prepare($sql);
        $like = '%' . $q . '%';
        $stmt->bindValue(':q', $q);
        $stmt->bindValue(':like', $like);
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode($stmt->fetchAll(\PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
    }

    /**
     * POST /pdv/api/venda
     * Body (JSON):
     * {
     *   "cliente_id": 1, "operador_id":1, "terminal_id":1, "turno_id":1,
     *   "itens":[{"produto_id":1,"quantidade":2,"preco_unit":50.00}],
     *   "pagamentos":[{"tipo":"dinheiro","valor":20.00},{"tipo":"credito","valor":30.00}]
     * }
     * Retorna: { ok:true, pedido_id: 123 }
     */
    public function apiVenda(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?: [];

        $clienteId  = (int)($data['cliente_id']  ?? 1);
        $operadorId = (int)($data['operador_id'] ?? 1);
        $terminalId = (int)($data['terminal_id'] ?? 1);
        $turnoId    = (int)($data['turno_id']    ?? 1);
        $itens      = $data['itens']       ?? [];
        $pagamentos = $data['pagamentos']  ?? [];

        if (empty($itens)) {
            http_response_code(422);
            echo json_encode(['ok'=>false,'error'=>'Nenhum item informado']);
            return;
        }

        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            // subtotal
            $subtotal = 0.0;
            foreach ($itens as $it) {
                $qtd   = (float)$it['quantidade'];
                $preco = (float)$it['preco_unit'];
                $subtotal += $qtd * $preco;
            }

            // cria pedido (canal PDV)
            $stmt = $pdo->prepare("
                INSERT INTO pedido (cliente_id, status, canal, entrega, pagamento, subtotal, frete, desconto, total, troco)
                VALUES (:cliente, 'novo', 'pdv', 'retirada', 'na_entrega', :subtotal, 0, 0, :total, 0)
            ");
            $stmt->execute([
                ':cliente'  => $clienteId,
                ':subtotal' => $subtotal,
                ':total'    => $subtotal,
            ]);
            $pedidoId = (int)$pdo->lastInsertId();

            // vincula meta do PDV (para o gatilho do caixa funcionar)
            $stmt = $pdo->prepare("
                INSERT INTO pdv_pedido_meta (pedido_id, terminal_id, turno_id, operador_id)
                VALUES (:p,:t,:u,:o)
            ");
            $stmt->execute([
                ':p'=>$pedidoId, ':t'=>$terminalId, ':u'=>$turnoId, ':o'=>$operadorId
            ]);

            // itens
            $stmtItem = $pdo->prepare("
                INSERT INTO item_pedido (pedido_id, produto_id, quantidade, preco_unit, desconto_unit)
                VALUES (:p,:prod,:qtd,:preco,0)
            ");
            foreach ($itens as $it) {
                $stmtItem->execute([
                    ':p'    => $pedidoId,
                    ':prod' => (int)$it['produto_id'],
                    ':qtd'  => (float)$it['quantidade'],
                    ':preco'=> (float)$it['preco_unit'],
                ]);
            }

            // pagamentos (dispara trigger -> mov_caixa)
            if (!empty($pagamentos)) {
                $stmtPg = $pdo->prepare("
                    INSERT INTO pedido_pagamento (pedido_id, tipo, valor) VALUES (:p,:t,:v)
                ");
                foreach ($pagamentos as $pg) {
                    $tipo = $pg['tipo']; // 'dinheiro','credito','debito','cheque','pix'
                    $val  = (float)$pg['valor'];
                    $stmtPg->execute([':p'=>$pedidoId, ':t'=>$tipo, ':v'=>$val]);
                }
            }

            $pdo->commit();
            echo json_encode(['ok'=>true,'pedido_id'=>$pedidoId], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
        }
    }
}
