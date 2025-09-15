<?php
namespace App\Controllers\Site;

use App\Core\Controller;
use App\Models\Pedido;

class PedidoController extends Controller {
    private function &carrinho() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['carrinho'])) $_SESSION['carrinho'] = [];
        return $_SESSION['carrinho'];
    }

    private function exigirClienteId(): int {
        // adapte ao seu login. Ex.: guarda em $_SESSION['cliente']['id']
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['cliente']['id'])) { header('Location: /login'); exit; }
        return (int)$_SESSION['cliente']['id'];
    }

    public function index() {
        $clienteId = $this->exigirClienteId();
        $pedidos = Pedido::listarDoCliente($clienteId);
        return $this->view('site/pedidos/index', compact('pedidos'));
    }

    public function ver($params) {
        $clienteId = $this->exigirClienteId();
        $pedidoId = (int)$params['id'];
        $pedido = Pedido::buscarDoCliente($clienteId, $pedidoId);
        if (!$pedido) return $this->redirect('/meus-pedidos');
        return $this->view('site/pedidos/ver', compact('pedido'));
    }

    public function finalizar() {
        $clienteId = $this->exigirClienteId();

        $carrinho = $this->carrinho();
        if (!$carrinho) return $this->redirect('/carrinho');

        // endereço/entrega/pagamento simplificados:
        $enderecoId = $_POST['endereco_id'] ?? null; // pode ser null (retirada)
        $entrega    = $_POST['entrega']    ?? 'retirada'; // 'retirada'|'entrega' (vide enum) :contentReference[oaicite:7]{index=7}
        $pagamento  = $_POST['pagamento']  ?? 'na_entrega'; // 'na_entrega'|'pix'|'cartao'|'gateway' :contentReference[oaicite:8]{index=8}

        try {
            $pedidoId = Pedido::criarComItens($clienteId, $enderecoId ? (int)$enderecoId : null, $entrega, $pagamento, $carrinho);
            $_SESSION['carrinho'] = [];
            return $this->redirect('/meus-pedidos/'.$pedidoId);
        } catch (\Throwable $e) {
            // TODO: flash message com $e->getMessage()
            return $this->redirect('/carrinho');
        }
    }
}
