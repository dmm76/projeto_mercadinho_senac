<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Core\Controller;
use App\Core\Url;
use App\Models\Produto;

final class ProdutoController extends Controller
{
    public function index(): void
    {
        $produtos = Produto::todosAtivos();
        $this->render('site/produtos/index', [
            'title'    => 'Produtos',
            'produtos' => $produtos,
        ]);
    }

    public function ver(int $id): void
    {
        $produto = Produto::encontrarAtivo($id);
        if (!$produto) {
            header('Location: ' . Url::to('/produtos'), true, 303);
            exit;
        }

        $this->render('site/produtos/ver', [
            'title'   => $produto['nome'] ?? 'Produto',
            'produto' => $produto,
        ]);
    }
}
