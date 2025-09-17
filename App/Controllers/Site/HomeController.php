<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Produto;

final class HomeController extends Controller
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

        $filters = [
            'q' => $filtersInput['q'],
            'ordem' => $filtersInput['ordem'],
            'favoritos' => $filtersInput['favoritos'],
        ];

        $this->render('site/home/index', [
            'title' => 'Mercadinho Borba Gato',
            'produtos' => $catalog['items'],
            'pagination' => $catalog['pagination'],
            'filters' => $filters,
            'clienteId' => $clienteId,
            'catalogBasePath' => '/',
        ]);
    }
}
