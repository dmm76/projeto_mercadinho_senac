public function apiFinalizarVenda($pedidoId): void
{
    header('Content-Type: application/json; charset=utf-8');
    $pedidoId = (int)$pedidoId;

    $data      = json_decode(file_get_contents('php://input'), true) ?? [];
    $linhas    = $data['itens'] ?? [];
    $desconto  = (float)($data['desconto'] ?? 0);

    if (!$pedidoId || empty($linhas)) {
        http_response_code(422);
        echo json_encode(['ok'=>false,'error'=>'Sem itens']);
        return;
    }

    // normaliza itens: soma quantidades por produto_id
    $itens = [];
    foreach ($linhas as $l) {
        $pid = (int)($l['produto_id'] ?? 0);
        $q   = (float)($l['quantidade'] ?? 0);
        if ($pid && $q > 0) {
            $itens[$pid] = ($itens[$pid] ?? 0) + $q;
        }
    }
    if (!$itens) {
        http_response_code(422);
        echo json_encode(['ok'=>false,'error'=>'Itens inválidos']);
        return;
    }

    $pdo = \App\DAO\Database::getConnection();
    $pdo->beginTransaction();

    try {
        // 1) limpa itens anteriores (se reabriu)
        $pdo->prepare("DELETE FROM item_pedido WHERE pedido_id=:p")->execute([':p'=>$pedidoId]);

        // 2) carrega preços atuais
        $ids = implode(',', array_map('intval', array_keys($itens)));
        $precos = $pdo->query("
            SELECT produto_id, 
                   COALESCE(preco_promocional, preco_venda) AS preco
              FROM preco
             WHERE produto_id IN ($ids)
        ")->fetchAll(\PDO::FETCH_KEY_PAIR);

        $stmtItem = $pdo->prepare("
            INSERT INTO item_pedido (pedido_id, produto_id, quantidade, preco_unit, desconto_unit)
            VALUES (:p,:prod,:qtd,:preco,0)
        ");
        $stmtMov = $pdo->prepare("
            INSERT INTO mov_estoque (produto_id, tipo, quantidade, origem, referencia_id, observacao)
            VALUES (:prod,'saida',:qtd,'pedido',:pid,'Saida por venda')
        ");

        $subtotal = 0.0;

        foreach ($itens as $prodId => $qtd) {
            $preco = (float)($precos[$prodId] ?? 0);
            // fallback: se sem preço em 'preco', recusa
            if ($preco <= 0) {
                throw new \RuntimeException("Produto $prodId sem preço cadastrado.");
            }

            $stmtItem->execute([
                ':p'=>$pedidoId, ':prod'=>$prodId, ':qtd'=>$qtd, ':preco'=>$preco
            ]);
            $stmtMov->execute([
                ':prod'=>$prodId, ':qtd'=>$qtd, ':pid'=>$pedidoId
            ]);

            $subtotal += $qtd * $preco;
        }

        $total = max(0, $subtotal - $desconto);

        // 3) atualiza pedido
        $pdo->prepare("
            UPDATE pedido
               SET subtotal=:sub, desconto=:desc, total=:tot, status='finalizado'
             WHERE id=:id
        ")->execute([
            ':sub'=>$subtotal, ':desc'=>$desconto, ':tot'=>$total, ':id'=>$pedidoId
        ]);

        $pdo->commit();
        echo json_encode(['ok'=>true,'pedido_id'=>$pedidoId,'subtotal'=>$subtotal,'total'=>$total]);
    } catch (\Throwable $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
    }
}
