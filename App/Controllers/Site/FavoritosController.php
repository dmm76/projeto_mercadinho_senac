<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Url;
use App\DAO\Database;
use App\DAO\FavoritoDAO;

final class FavoritosController
{
    public function toggle(): void
    {
        Auth::requireLogin('Faca login para gerenciar seus favoritos.', '/');

        if (!Csrf::check($_POST['csrf'] ?? null)) {
            Flash::set('error', 'Sessao expirada. Tente novamente.');
            $this->redirect($_POST['redirect'] ?? '/');
        }

        $produtoId = (int)($_POST['produto_id'] ?? 0);
        if ($produtoId <= 0) {
            Flash::set('error', 'Produto invalido.');
            $this->redirect($_POST['redirect'] ?? '/');
        }

        $clienteId = Auth::clienteId();
        if ($clienteId === null) {
            Flash::set('error', 'Nao foi possivel identificar o cliente.');
            $this->redirect('/login');
        }

        $dao = new FavoritoDAO(Database::getConnection());
        $isFavorito = $dao->toggle($clienteId, $produtoId);
        Flash::set('success', $isFavorito ? 'Produto adicionado aos favoritos.' : 'Produto removido dos favoritos.');
        $this->redirect($_POST['redirect'] ?? '/');
    }

    private function redirect(string $path): void
    {
        $url = str_starts_with($path, 'http') ? $path : Url::to($path);
        header('Location: ' . $url, true, 303);
        exit;
    }
}
