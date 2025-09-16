<?php
declare(strict_types=1);

namespace App\Controllers\Site;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Url;
use App\DAO\Database;
use App\Models\Pedido; // <- AJUSTE: App\Model, não App\Models
use PDO;

final class PedidoController extends Controller
{
    private function &carrinho(): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }
        return $_SESSION['carrinho'];
    }

    private function exigirClienteId(): int
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $cid = (int)($_SESSION['cliente_id'] ?? 0); // <- AJUSTE: usamos cliente_id plano na sessão
        if ($cid <= 0) {
            Flash::set('error', 'Faça login para continuar.');
            header('Location: ' . Url::to('/login'), true, 303);
            exit;
        }
        return $cid;
    }

    public function index(): void
    {
        $clienteId = $this->exigirClienteId();
        $pedidos = Pedido::listarDoCliente($clienteId);
        $this->render('site/pedidos/index', ['title' => 'Meus pedidos', 'pedidos' => $pedidos]);
    }

    public function ver(array $params): void
    {
        $clienteId = $this->exigirClienteId();
        $pedidoId = (int)($params['id'] ?? 0);

        if ($pedidoId <= 0) {
            header('Location: ' . Url::to('/conta/pedidos'), true, 303);
            exit;
        }

        $pedido = Pedido::buscarDoCliente($clienteId, $pedidoId);
        if (!$pedido) {
            Flash::set('error', 'Pedido não encontrado.');
            header('Location: ' . Url::to('/conta/pedidos'), true, 303);
            exit;
        }

        $this->render('site/pedidos/ver', ['title' => 'Pedido #'.$pedidoId, 'pedido' => $pedido]);
    }

    /** Exibe o checkout com os endereços do cliente */
    public function checkout(): void
    {
        $clienteId = $this->exigirClienteId();
        $carrinho = $this->carrinho();

        if (!$carrinho) {
            Flash::set('error', 'Seu carrinho está vazio.');
            header('Location: ' . Url::to('/carrinho'), true, 303);
            exit;
        }

        $pdo = Database::getConnection();
        $q = $pdo->prepare(
            "SELECT id, rotulo, logradouro, numero, bairro, cidade, uf, cep, principal
               FROM endereco
              WHERE cliente_id = ?
           ORDER BY principal DESC, criado_em DESC"
        );
        $q->execute([$clienteId]);
        $enderecos = $q->fetchAll(PDO::FETCH_ASSOC);

        $this->render('site/checkout/index', [
            'title'     => 'Checkout',
            'enderecos' => $enderecos,
            'carrinho'  => $carrinho,
        ]);
    }

    /** Finaliza o pedido (POST /checkout) */
    public function finalizar(): void
    {
        $clienteId = $this->exigirClienteId();

        if (!Csrf::check($_POST['csrf'] ?? null)) {
            Flash::set('error', 'Token inválido.');
            header('Location: ' . Url::to('/checkout'), true, 303);
            exit;
        }

        $carrinho = $this->carrinho();
        if (!$carrinho) {
            Flash::set('error', 'Carrinho vazio.');
            header('Location: ' . Url::to('/carrinho'), true, 303);
            exit;
        }

        $entrega    = $_POST['entrega']   ?? 'retirada';     // 'retirada' | 'entrega'
        $pagamento  = $_POST['pagamento'] ?? 'na_entrega';   // 'na_entrega' | 'pix' | 'cartao' | 'gateway'
        $enderecoId = isset($_POST['endereco_id']) ? (int)$_POST['endereco_id'] : null;

        // Validar enums conforme schema
        $entregaOk = in_array($entrega, ['retirada', 'entrega'], true);
        $pagOk     = in_array($pagamento, ['na_entrega', 'pix', 'cartao', 'gateway'], true);
        if (!$entregaOk || !$pagOk) {
            Flash::set('error', 'Dados inválidos no checkout.');
            header('Location: ' . Url::to('/checkout'), true, 303);
            exit;
        }

        // Se for entrega domiciliar: endereço é obrigatório e deve pertencer ao cliente
        if ($entrega === 'entrega') {
            if (!$enderecoId) {
                Flash::set('error', 'Selecione um endereço de entrega.');
                header('Location: ' . Url::to('/checkout'), true, 303);
                exit;
            }
            $pdo = Database::getConnection();
            $chk = $pdo->prepare("SELECT 1 FROM endereco WHERE id = ? AND cliente_id = ?");
            $chk->execute([$enderecoId, $clienteId]);
            if (!$chk->fetchColumn()) {
                Flash::set('error', 'Endereço inválido.');
                header('Location: ' . Url::to('/checkout'), true, 303);
                exit;
            }
        } else {
            $enderecoId = null; // retirada não precisa endereço
        }

        try {
            $pedidoId = Pedido::criarComItens(
                $clienteId,
                $enderecoId,
                $entrega,
                $pagamento,
                $carrinho
            );

            // Limpar carrinho após criar pedido
            $_SESSION['carrinho'] = [];

            Flash::set('success', 'Pedido criado com sucesso!');
            header('Location: ' . Url::to('/conta/pedidos/' . $pedidoId), true, 303);
            exit;
        } catch (\Throwable $e) {
            // Em produção, logue o erro
            Flash::set('error', 'Não foi possível finalizar: ' . $e->getMessage());
            header('Location: ' . Url::to('/checkout'), true, 303);
            exit;
        }
    }
}
