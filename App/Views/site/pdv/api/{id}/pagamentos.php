public function apiAdicionarPagamento($pedidoId): void
{
    header('Content-Type: application/json; charset=utf-8');
    $pedidoId = (int)$pedidoId;

    $data  = json_decode(file_get_contents('php://input'), true) ?? [];
    $tipo  = (string)($data['tipo']  ?? '');
    $valor = (float) ($data['valor'] ?? 0);

    if (!$pedidoId || !$tipo || $valor <= 0) {
        http_response_code(422);
        echo json_encode(['ok'=>false,'error'=>'Dados inválidos']);
        return;
    }

    try {
        $pdo = \App\DAO\Database::getConnection();
        $pdo->prepare("
            INSERT INTO pedido_pagamento (pedido_id, tipo, valor) VALUES (:p,:t,:v)
        ")->execute([':p'=>$pedidoId, ':t'=>$tipo, ':v'=>$valor]);

        // A trigger trg_pagamento_insere_movcaixa cuidará do mov_caixa
        echo json_encode(['ok'=>true]);
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
    }
}
