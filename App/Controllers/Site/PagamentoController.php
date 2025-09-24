<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Core\Controller;
use App\Core\Flash;
use App\Core\Url;
use App\Models\Pedido;
use App\Services\PixPaymentService;

final class PagamentoController extends Controller
{
    private function exigirClienteId(): int
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $clienteId = (int)($_SESSION['cliente_id'] ?? 0);
        if ($clienteId <= 0) {
            Flash::set('error', 'Faca login para acessar o pagamento.');
            header('Location: ' . Url::to('/login'), true, 303);
            exit;
        }
        return $clienteId;
    }

    private function carregarPedido(int $clienteId, int $pedidoId): array
    {
        if ($pedidoId <= 0) {
            Flash::set('error', 'Pedido invalido.');
            header('Location: ' . Url::to('/conta/pedidos'), true, 303);
            exit;
        }
        $pedido = Pedido::buscarDoCliente($clienteId, $pedidoId);
        if (!$pedido) {
            Flash::set('error', 'Pedido nao encontrado.');
            header('Location: ' . Url::to('/conta/pedidos'), true, 303);
            exit;
        }
        if (($pedido['pagamento'] ?? '') !== 'pix') {
            Flash::set('error', 'Este pedido nao usa pagamento PIX.');
            header('Location: ' . Url::to('/conta/pedidos/' . $pedidoId), true, 303);
            exit;
        }
        return $pedido;
    }

    public function pix(int $pedidoId): void
    {
        $clienteId = $this->exigirClienteId();
        $pedido = $this->carregarPedido($clienteId, $pedidoId);

        $service = new PixPaymentService();
        try {
            $payment = $service->createPayment($pedidoId);
        } catch (\Throwable $e) {
            Flash::set('error', 'Nao foi possivel gerar o PIX: ' . $e->getMessage());
            header('Location: ' . Url::to('/conta/pedidos/' . $pedidoId), true, 303);
            exit;
        }

        $this->render('site/pagamentos/pix', [
            'title' => 'Pagamento PIX',
            'pedido' => $pedido,
            'payment' => $payment,
        ]);
    }

    public function pixStatus(int $pedidoId): void
    {
        $clienteId = $this->exigirClienteId();
        $this->carregarPedido($clienteId, $pedidoId);

        $service = new PixPaymentService();
        try {
            $payment = $service->fetchPayment($pedidoId);
        } catch (\Throwable $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'error' => true,
                'message' => 'Erro ao consultar pagamento PIX.',
            ]);
            return;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'error' => false,
            'payment' => [
                'status' => $payment['status'] ?? null,
                'status_detail' => $payment['status_detail'] ?? null,
                'qr_code_base64' => $payment['qr_code_base64'] ?? null,
                'qr_code' => $payment['qr_code'] ?? null,
                'ticket_url' => $payment['ticket_url'] ?? null,
                'expires_at' => $payment['date_of_expiration'] ?? null,
            ],
        ]);
    }
}


