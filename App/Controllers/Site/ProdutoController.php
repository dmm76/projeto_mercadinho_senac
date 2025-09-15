<?php
namespace App\Controllers\Site;

use App\Core\Controller;
use App\Models\Produto;

class ProdutoController extends Controller {
    public function index() {
        $produtos = Produto::todosAtivos();
        return $this->view('site/produtos/index', compact('produtos'));
    }

    public function ver($params) {
        $id = (int)$params['id'];
        $produto = Produto::encontrarAtivo($id);
        if (!$produto) return $this->redirect('/produtos');
        return $this->view('site/produtos/ver', compact('produto'));
    }
}
