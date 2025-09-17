<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\DAO\Database;
use App\DAO\MensagemDAO;

final class MensagensController extends BaseAdminController
{
    public function index(): void
    {
        $filters = [
            'q'      => trim((string)($_GET['q'] ?? '')),
            'status' => trim((string)($_GET['status'] ?? '')),
        ];

        $dao = new MensagemDAO(Database::getConnection());
        $mensagens = $dao->list($filters);

        $this->render('admin/mensagens/index', [
            'title'     => 'Mensagens',
            'mensagens' => $mensagens,
            'filters'   => $filters,
        ]);
    }
}
