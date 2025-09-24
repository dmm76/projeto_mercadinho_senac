<?php

declare(strict_types=1);

namespace App\Services;

use App\DAO\Database;
use MercadoPago\Payment;
use PDO;

final class PixPaymentService
{
    private static bool $tableChecked = false;

    /**
     * Cria (ou recupera) um pagamento PIX no Mercado Pago e persiste o resumo no banco.
     *
     * @return array<string,mixed>
     */
    public function createPayment(int $pedidoId): array
    {
        $pdo = Database::getConnection();
        $pedido = $this->loadPedido($pdo, $pedidoId);
        if (($pedido['pagamento'] ?? '') !== 'pix') {
            throw new \RuntimeException('Pedido nao esta configurado para PIX.');
        }

        $existingId = $this->loadPixPaymentId($pdo, $pedidoId);
        if ($existingId > 0) {
            return $this->fetchPayment($pedidoId);
        }

        $externalRef = $this->ensureOrderCode($pdo, $pedido);

        $payment = new Payment();
        $payment->transaction_amount = (float)($pedido['total'] ?? 0.0);
        $payment->description = 'Pedido #' . $pedidoId;
        $payment->payment_method_id = 'pix';
        $payment->external_reference = $externalRef;
        $payment->payer = $this->buildPayerPayload($pedido);

        if ($payment->save() === false || !isset($payment->id)) {
            throw new \RuntimeException('Erro ao criar pagamento PIX.');
        }

        $data = $this->mapPayment($payment);
        $this->persistPayment($pdo, $pedidoId, $data);
        return $data;
    }

    /**
     * Recupera o pagamento PIX no Mercado Pago e sincroniza o status local.
     *
     * @return array<string,mixed>
     */
    public function fetchPayment(int $pedidoId): array
    {
        $pdo = Database::getConnection();
        $paymentId = $this->loadPixPaymentId($pdo, $pedidoId);
        if ($paymentId <= 0) {
            throw new \RuntimeException('Pagamento PIX nao encontrado para este pedido.');
        }

        $payment = Payment::find_by_id($paymentId);
        if (!$payment) {
            throw new \RuntimeException('Falha ao consultar pagamento PIX.');
        }

        $data = $this->mapPayment($payment);
        $this->persistPayment($pdo, $pedidoId, $data);
        return $data;
    }

    /**
     * @return array<string,mixed>
     */
    private function loadPedido(PDO $pdo, int $pedidoId): array
    {
        $sql = 'SELECT p.id, p.total, p.codigo_externo, p.pagamento, u.nome AS cliente_nome, u.email AS cliente_email, c.cpf AS cliente_cpf'
            . ' FROM pedido p'
            . ' JOIN cliente c ON c.id = p.cliente_id'
            . ' JOIN usuario u ON u.id = c.usuario_id'
            . ' WHERE p.id = :id LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $pedidoId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            throw new \RuntimeException('Pedido nao encontrado.');
        }
        return $row;
    }

    private function ensureOrderCode(PDO $pdo, array &$pedido): string
    {
        $codigo = $pedido['codigo_externo'] ?? '';
        if (!is_string($codigo) || $codigo === '') {
            $codigo = sprintf('PED-%06d', (int)($pedido['id'] ?? 0));
            $upd = $pdo->prepare('UPDATE pedido SET codigo_externo = :codigo WHERE id = :id');
            $upd->execute([
                ':codigo' => $codigo,
                ':id' => (int)($pedido['id'] ?? 0),
            ]);
            $pedido['codigo_externo'] = $codigo;
        }
        return $codigo;
    }

    /**
     * @return array<string,mixed>
     */
    private function buildPayerPayload(array $pedido): array
    {
        $nome = trim((string)($pedido['cliente_nome'] ?? 'Cliente'));
        $email = trim((string)($pedido['cliente_email'] ?? ''));
        $cpf = preg_replace('/\D+/', '', (string)($pedido['cliente_cpf'] ?? ''));

        $parts = preg_split('/\s+/', $nome);
        $firstName = $parts[0] ?? 'Cliente';
        $lastName = $parts[1] ?? ($parts[0] ?? '');

        $payer = [
            'email' => $email !== '' ? $email : 'comprador@email.com',
            'first_name' => $firstName,
            'last_name' => $lastName,
        ];

        if ($cpf !== '') {
            $payer['identification'] = [
                'type' => 'CPF',
                'number' => $cpf,
            ];
        }

        return $payer;
    }

    /**
     * @return array<string,mixed>
     */
    private function mapPayment($payment): array
    {
        $poi = null;
        if (isset($payment->point_of_interaction)) {
            $poiContainer = $payment->point_of_interaction;
            if (is_array($poiContainer)) {
                $poi = $poiContainer['transaction_data'] ?? null;
            } elseif (is_object($poiContainer) && property_exists($poiContainer, 'transaction_data')) {
                $poi = $poiContainer->transaction_data;
            }
        }

        $qrCode = null;
        $qrCodeBase64 = null;
        $ticketUrl = null;

        if (is_array($poi)) {
            $qrCode = $poi['qr_code'] ?? null;
            $qrCodeBase64 = $poi['qr_code_base64'] ?? null;
            $ticketUrl = $poi['ticket_url'] ?? null;
        } elseif (is_object($poi)) {
            $qrCode = $poi->qr_code ?? null;
            $qrCodeBase64 = $poi->qr_code_base64 ?? null;
            $ticketUrl = $poi->ticket_url ?? null;
        }

        return [
            'id' => (int)($payment->id ?? 0),
            'status' => (string)($payment->status ?? ''),
            'status_detail' => (string)($payment->status_detail ?? ''),
            'description' => (string)($payment->description ?? ''),
            'external_reference' => (string)($payment->external_reference ?? ''),
            'transaction_amount' => (float)($payment->transaction_amount ?? 0),
            'date_created' => (string)($payment->date_created ?? ''),
            'date_approved' => (string)($payment->date_approved ?? ''),
            'date_of_expiration' => (string)($payment->date_of_expiration ?? ''),
            'qr_code' => $qrCode,
            'qr_code_base64' => $qrCodeBase64,
            'ticket_url' => $ticketUrl,
        ];
    }

    /**
     * @param array<string,mixed> $data
     */
    private function persistPayment(PDO $pdo, int $pedidoId, array $data): void
    {
        $this->ensureTable($pdo);
        $expiresAt = $this->normalizeDate($data['date_of_expiration'] ?? null);
        $stmt = $pdo->prepare(
            'INSERT INTO pedido_pix (pedido_id, mp_payment_id, status, status_detail, qr_code, qr_code_base64, ticket_url, expires_at)'
            . ' VALUES (:pedido_id, :mp_payment_id, :status, :status_detail, :qr_code, :qr_code_base64, :ticket_url, :expires_at)'
            . ' ON DUPLICATE KEY UPDATE'
            . '   mp_payment_id = VALUES(mp_payment_id),'
            . '   status = VALUES(status),'
            . '   status_detail = VALUES(status_detail),'
            . '   qr_code = VALUES(qr_code),'
            . '   qr_code_base64 = VALUES(qr_code_base64),'
            . '   ticket_url = VALUES(ticket_url),'
            . '   expires_at = VALUES(expires_at),'
            . '   updated_at = CURRENT_TIMESTAMP'
        );
        $stmt->execute([
            ':pedido_id' => $pedidoId,
            ':mp_payment_id' => (int)($data['id'] ?? 0),
            ':status' => (string)($data['status'] ?? ''),
            ':status_detail' => (string)($data['status_detail'] ?? ''),
            ':qr_code' => $data['qr_code'] ?? null,
            ':qr_code_base64' => $data['qr_code_base64'] ?? null,
            ':ticket_url' => $data['ticket_url'] ?? null,
            ':expires_at' => $expiresAt,
        ]);

        if (($data['status'] ?? '') === 'approved') {
            $updatePedido = $pdo->prepare(
                'UPDATE pedido SET status = CASE WHEN status = "novo" THEN "em_separacao" ELSE status END WHERE id = :id'
            );
            $updatePedido->execute([':id' => $pedidoId]);
        }
    }

    private function loadPixPaymentId(PDO $pdo, int $pedidoId): int
    {
        $this->ensureTable($pdo);
        $stmt = $pdo->prepare('SELECT mp_payment_id FROM pedido_pix WHERE pedido_id = :pedido_id LIMIT 1');
        $stmt->execute([':pedido_id' => $pedidoId]);
        $value = $stmt->fetchColumn();
        return $value !== false ? (int) $value : 0;
    }

    private function ensureTable(PDO $pdo): void
    {
        if (self::$tableChecked) {
            return;
        }

        $sql = 'CREATE TABLE IF NOT EXISTS pedido_pix ('
            . ' pedido_id INT NOT NULL PRIMARY KEY,'
            . ' mp_payment_id BIGINT NOT NULL,'
            . ' status VARCHAR(32) NOT NULL,'
            . ' status_detail VARCHAR(64) DEFAULT NULL,'
            . ' qr_code TEXT NULL,'
            . ' qr_code_base64 MEDIUMTEXT NULL,'
            . ' ticket_url VARCHAR(255) NULL,'
            . ' expires_at DATETIME NULL,'
            . ' created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,'
            . ' updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,'
            . ' CONSTRAINT fk_pedido_pix_pedido FOREIGN KEY (pedido_id) REFERENCES pedido(id) ON DELETE CASCADE ON UPDATE CASCADE'
            . ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
        $pdo->exec($sql);
        self::$tableChecked = true;
    }

    private function normalizeDate(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return null;
        }
        return gmdate('Y-m-d H:i:s', $timestamp);
    }
}
