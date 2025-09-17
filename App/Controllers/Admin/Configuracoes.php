<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

final class ConfiguracoesController extends BaseAdminController
{
    public function index(): void
    {
        $title = 'Configurações';
        /** @var array<string,mixed> $config */
        $config = []; // TODO: carregar/salvar de uma tabela config ou arquivo
        require __DIR__ . '/../../Views/admin/configuracoes/index.php';
    }
}
