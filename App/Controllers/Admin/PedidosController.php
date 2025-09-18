<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Flash;
use App\Core\Url;
use App\DAO\Database;
use PDO;
use PDOException;

final class PedidosController extends BaseAdminController
{
    private PDO $pdo;

    /**
     * @var array<int, string>
     */
    private array $allowedStatuses;

    private const STATUS_GROUPS = [
        'pendente' => ['pendente', 'novo', 'em_separacao'],
        'pago' => ['pago', 'pronto'],
        'enviado' => ['enviado', 'em_transporte', 'finalizado'],
        'cancelado' => ['cancelado'],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->pdo = Database::getConnection();
        $this->allowedStatuses = $this->detectAllowedStatuses();
    }

    public function index(): void
    {
        $filters = [
            'status' => trim((string)($_GET['status'] ?? '')),
            'q' => trim((string)($_GET['q'] ?? '')),
            'de' => trim((string)($_GET['de'] ?? '')),
            'ate' => trim((string)($_GET['ate'] ?? '')),
        ];

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
            $dbStatuses = $this->dbStatusesFor($filters['status']);
            if ($dbStatuses) {
                $placeholders = [];
                foreach ($dbStatuses as $idx => $status) {
                    $ph = ':status_' . $idx;
                    $placeholders[] = $ph;
                    $params[$ph] = $status;
                }
                $sql .= ' AND p.status IN (' . implode(', ', $placeholders) . ')';
            }
        }

        if ($filters['q'] !== '') {
            $sql .= ' AND (u.nome LIKE :qNome OR e.nome LIKE :qEndereco OR p.id = :idq)';
            $likeValue = "%{$filters['q']}%";
            $params[':qNome'] = $likeValue;
            $params[':qEndereco'] = $likeValue;
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

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $pedidos = array_map(function (array $pedido): array {
            $raw = (string)($pedido['status'] ?? '');
            $pedido['status_raw'] = $raw;
            $pedido['status'] = $this->normalizeStatus($raw);
            return $pedido;
        }, $pedidos);

        $this->render('admin/pedidos/index', [
            'title' => 'Pedidos',
            'pedidos' => $pedidos,
            'filters' => $filters,
        ]);
    }

    public function show(int $id): void
    {
        $pedido = $this->loadPedido($id);
        if ($pedido === null) {
            Flash::set('error', 'Pedido nao encontrado.');
            $this->redirect('/admin/pedidos');
        }

        $itens = $this->loadItens($id);

        $this->render('admin/pedidos/ver', [
            'title' => 'Pedido #' . $pedido['id'],
            'pedido' => $pedido,
            'itens' => $itens,
        ]);
    }

    public function marcarPago(int $id): void
    {
        $this->changeStatus($id, 'pago', ['pendente'], 'Pedido marcado como pago.');
    }

    public function enviar(int $id): void
    {
        $this->changeStatus($id, 'enviado', ['pago'], 'Pedido marcado como enviado.');
    }

    public function cancelar(int $id): void
    {
        $this->changeStatus($id, 'cancelado', ['pendente', 'pago'], 'Pedido cancelado.');
    }

    private function changeStatus(int $id, string $targetStatus, array $allowedCurrent, string $successMessage): void
    {
        $statusInfo = $this->getStatus($id);
        if ($statusInfo === null) {
            Flash::set('error', 'Pedido nao encontrado.');
            $this->redirect('/admin/pedidos');
        }

        if (!in_array($statusInfo['normalized'], $allowedCurrent, true)) {
            Flash::set('error', 'Transicao de status nao permitida para este pedido.');
            $this->redirect('/admin/pedidos/' . $id);
        }

        $novoStatus = $this->chooseStorageStatus($targetStatus);

        try {
            $stmt = $this->pdo->prepare('UPDATE pedido SET status = :status WHERE id = :id');
            $stmt->execute([
                ':status' => $novoStatus,
                ':id' => $id,
            ]);
            Flash::set('success', $successMessage);
        } catch (PDOException $e) {
            Flash::set('error', 'Nao foi possivel atualizar o status do pedido.');
        }

        $this->redirect('/admin/pedidos/' . $id);
    }

    private function loadPedido(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.id,
                    p.codigo_externo,
                    p.status,
                    p.entrega,
                    p.pagamento,
                    p.subtotal,
                    p.frete,
                    p.desconto,
                    p.total,
                    p.criado_em,
                    u.nome AS cliente_nome,
                    u.email AS cliente_email,
                    c.telefone AS cliente_telefone,
                    e.rotulo AS endereco_rotulo,
                    e.logradouro,
                    e.numero,
                    e.bairro,
                    e.cidade,
                    e.uf,
                    e.cep
               FROM pedido p
          LEFT JOIN cliente c ON c.id = p.cliente_id
          LEFT JOIN usuario u ON u.id = c.usuario_id
          LEFT JOIN endereco e ON e.id = p.endereco_id
              WHERE p.id = ?
              LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }

        $row['status_raw'] = (string)($row['status'] ?? '');
        $row['status'] = $this->normalizeStatus($row['status_raw']);
        return $row;
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function loadItens(int $id): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT ip.produto_id,
                    p.nome,
                    ip.quantidade,
                    ip.preco_unit AS preco,
                    (ip.quantidade * ip.preco_unit) AS subtotal
               FROM item_pedido ip
               JOIN produto p ON p.id = ip.produto_id
              WHERE ip.pedido_id = ?
           ORDER BY ip.id ASC'
        );
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @return array{raw:string,normalized:string}|null
     */
    private function getStatus(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT status FROM pedido WHERE id = ?');
        $stmt->execute([$id]);
        $status = $stmt->fetchColumn();
        if ($status === false) {
            return null;
        }
        $raw = (string)$status;
        return [
            'raw' => $raw,
            'normalized' => $this->normalizeStatus($raw),
        ];
    }

    private function redirect(string $path): void
    {
        header('Location: ' . Url::to($path), true, 303);
        exit;
    }

    /**
     * @return array<int, string>
     */
    private function detectAllowedStatuses(): array
    {
        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM pedido LIKE 'status'");
            if ($stmt !== false) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (is_array($row) && isset($row['Type']) && is_string($row['Type'])) {
                    if (preg_match_all("/'([^']+)'/", $row['Type'], $matches)) {
                        return array_map('strtolower', $matches[1]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignora: se nao conseguir detectar, mantemos lista vazia
        }
        return [];
    }

    /**
     * @return array<int, string>
     */
    private function dbStatusesFor(string $normalized): array
    {
        $normalized = strtolower($normalized);
        $candidates = self::STATUS_GROUPS[$normalized] ?? [$normalized];
        $result = [];
        foreach ($candidates as $status) {
            if (!$this->allowedStatuses || in_array(strtolower($status), $this->allowedStatuses, true)) {
                $result[] = $status;
            }
        }
        if (!$result) {
            $result[] = $normalized;
        }
        return array_values(array_unique($result));
    }

    private function normalizeStatus(string $raw): string
    {
        $rawLower = strtolower($raw);
        foreach (self::STATUS_GROUPS as $normalized => $candidates) {
            foreach ($candidates as $candidate) {
                if ($rawLower === strtolower($candidate)) {
                    return $normalized;
                }
            }
        }
        return $rawLower !== '' ? $rawLower : 'pendente';
    }

    private function chooseStorageStatus(string $normalized): string
    {
        $normalized = strtolower($normalized);
        $candidates = self::STATUS_GROUPS[$normalized] ?? [$normalized];
        foreach ($candidates as $candidate) {
            if (!$this->allowedStatuses || in_array(strtolower($candidate), $this->allowedStatuses, true)) {
                return $candidate;
            }
        }
        return $normalized;
    }
}
