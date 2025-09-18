<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Url;
use App\DAO\Database;
use App\DAO\FavoritoDAO;
use App\Models\Produto;

final class ProdutoController extends Controller
{
    public function index(): void
    {
        $clienteId = Auth::clienteId();
        $filtersInput = [
            'q' => trim((string)($_GET['q'] ?? '')),
            'ordem' => (string)($_GET['ordem'] ?? 'novidades'),
            'favoritos' => isset($_GET['favoritos']) && $_GET['favoritos'] === '1',
            'page' => (int)($_GET['page'] ?? 1),
        ];

        $catalog = Produto::buscarParaLoja([
            'q' => $filtersInput['q'],
            'ordem' => $filtersInput['ordem'],
            'page' => $filtersInput['page'],
            'per_page' => 12,
            'cliente_id' => $clienteId,
            'somente_favoritos' => $filtersInput['favoritos'],
            'favoritos_primeiro' => true,
        ]);

        $this->render('site/produtos/index', [
            'title' => 'Produtos',
            'produtos' => $catalog['items'],
            'pagination' => $catalog['pagination'],
            'filters' => [
                'q' => $filtersInput['q'],
                'ordem' => $filtersInput['ordem'],
                'favoritos' => $filtersInput['favoritos'],
            ],
            'clienteId' => $clienteId,
            'catalogBasePath' => '/produtos',
        ]);
    }

    public function ver(int $id): void
    {
        $produto = Produto::encontrarAtivo($id);
        if (!$produto) {
            header('Location: ' . Url::to('/produtos'), true, 303);
            exit;
        }

        $clienteId = Auth::clienteId();
        $isFavorito = false;
        $produtoId = isset($produto['id']) ? (int) $produto['id'] : 0;
        if ($clienteId !== null && $produtoId > 0) {
            $dao = new FavoritoDAO(Database::getConnection());
            $isFavorito = $dao->exists($clienteId, $produtoId);
        }

        $this->render('site/produtos/ver', [
            'title'      => $produto['nome'] ?? 'Produto',
            'produto'    => $produto,
            'clienteId'  => $clienteId,
            'isFavorito' => $isFavorito,
        ]);
    }
}
