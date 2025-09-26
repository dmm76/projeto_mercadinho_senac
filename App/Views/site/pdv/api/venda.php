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
        ")->execute([':c'=>$clienteId]);

        $pedidoId = (int)$pdo->lastInsertId();

        // meta PDV (necessária para a trigger do caixa)
        $pdo->prepare("
            INSERT INTO pdv_pedido_meta (pedido_id, terminal_id, turno_id, operador_id)
            VALUES (:p,:t,:u,:o)
        ")->execute([
            ':p'=>$pedidoId, ':t'=>$terminalId, ':u'=>$turnoId, ':o'=>$operadorId
        ]);

        $pdo->commit();
        http_response_code(201);
        echo json_encode(['ok'=>true, 'id'=>$pedidoId], JSON_UNESCAPED_UNICODE);
    } catch (\Throwable $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
    }
}
