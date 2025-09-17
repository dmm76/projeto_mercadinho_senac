<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\DAO\Database;
use PDO;

final class PedidosController extends BaseAdminController
{
    public function index(): void
    {
        $filters = [
            'status' => trim((string)($_GET['status'] ?? '')),
            'q'      => trim((string)($_GET['q'] ?? '')),
            'de'     => trim((string)($_GET['de'] ?? '')),
            'ate'    => trim((string)($_GET['ate'] ?? '')),
        ];

        $pdo = Database::getConnection();

        $sql = <<<SQL
SELECT
    p.id,
    COALESCE(u.nome, e.nome, CONCAT('Cliente #', p.cliente_id)) AS cliente,
    p.pagamento,
    p.status,
    COUNT(ip.id) AS qtd_itens,
    p.total,
    p.criado_em
FROM pedido p
LEFT JOIN cliente c ON c.id = p.cliente_id
LEFT JOIN usuario u ON u.id = c.usuario_id
LEFT JOIN endereco e ON e.id = p.endereco_id
LEFT JOIN item_pedido ip ON ip.pedido_id = p.id
WHERE 1 = 1
SQL;

        $params = [];

        if ($filters['status'] !== '') {
            $sql .= ' AND p.status = :status';
            $params[':status'] = $filters['status'];
        }

        if ($filters['q'] !== '') {
            $sql .= ' AND (u.nome LIKE :q OR e.nome LIKE :q OR p.id = :idq)';
            $params[':q'] = "%{$filters['q']}%";
            $params[':idq'] = ctype_digit($filters['q']) ? (int)$filters['q'] : 0;
        }

        if ($filters['de'] !== '') {
            $sql .= ' AND p.criado_em >= :de';
            $params[':de'] = $filters['de'] . ' 00:00:00';
        }

        if ($filters['ate'] !== '') {
            $sql .= ' AND p.criado_em <= :ate';
            $params[':ate'] = $filters['ate'] . ' 23:59:59';
        }

        $sql .= ' GROUP BY p.id, cliente, p.pagamento, p.status, p.total, p.criado_em';
        $sql .= ' ORDER BY p.criado_em DESC LIMIT 100';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $this->render('admin/pedidos/index', [
            'title'   => 'Pedidos',
            'pedidos' => $pedidos,
            'filters' => $filters,
        ]);
    }
}
