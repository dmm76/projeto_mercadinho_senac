<?php declare(strict_types=1);

namespace App\Controllers\Site;

use App\Core\Controller;
use App\Models\Produto;

class CarrinhoController extends Controller {
    private function &carrinho() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['carrinho'])) $_SESSION['carrinho'] = []; // [produto_id => ['produto'=>row,'quantidade'=>decimal]]
        return $_SESSION['carrinho'];
    }

    public function index() {
        $carrinho = $this->carrinho();
        $total = 0.0;
        foreach ($carrinho as $it) {
            $total += (float)$it['produto']['preco_atual'] * (float)$it['quantidade'];
        }
        return $this->view('site/carrinho/index', compact('carrinho','total'));
    }

    public function adicionar($params) {
        $id = (int)$params['id'];
        $produto = Produto::encontrarAtivo($id);
        if (!$produto) return $this->redirect('/produtos');

        $step = ($produto['peso_variavel'] || $produto['unidade_sigla']==='KG') ? 0.001 : 1;
        $q = isset($_POST['quantidade']) ? (float)$_POST['quantidade'] : 1;
        if ($q <= 0) $q = $step;

        $carrinho = &$this->carrinho();
        if (!isset($carrinho[$id])) $carrinho[$id] = ['produto'=>$produto, 'quantidade'=>0];
        $carrinho[$id]['quantidade'] = round($carrinho[$id]['quantidade'] + $q, 3);

        return $this->redirect('/carrinho');
    }

    public function atualizar($params) {
        $id = (int)$params['id'];
        $carrinho = &$this->carrinho();
        if (!isset($carrinho[$id])) return $this->redirect('/carrinho');

        $q = max(0.0, (float)($_POST['quantidade'] ?? 0));
        if ($q <= 0) unset($carrinho[$id]);
        else $carrinho[$id]['quantidade'] = round($q, 3);

        return $this->redirect('/carrinho');
    }

    public function remover($params) {
        $id = (int)$params['id'];
        $carrinho = &$this->carrinho();
        unset($carrinho[$id]);
        return $this->redirect('/carrinho');
    }
}
