<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Url;
use App\DAO\Database;
use App\DAO\MensagemDAO;

final class MensagensController extends BaseAdminController
{
    private MensagemDAO $dao;

    public function __construct()
    {
        parent::__construct();
        $this->dao = new MensagemDAO(Database::getConnection());
    }

    public function index(): void
    {
        $filters = [
            'q'      => trim((string)($_GET['q'] ?? '')),
            'status' => trim((string)($_GET['status'] ?? '')),
        ];

        $mensagens = $this->dao->list($filters);

        $this->render('admin/mensagens/index', [
            'title'     => 'Mensagens',
            'mensagens' => $mensagens,
            'filters'   => $filters,
        ]);
    }

    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            Flash::set('error', 'Mensagem inválida.');
            $this->redirect('/admin/mensagens');
        }

        $mensagem = $this->dao->find($id);
        if ($mensagem === null) {
            Flash::set('error', 'Mensagem não encontrada.');
            $this->redirect('/admin/mensagens');
        }

        $this->render('admin/mensagens/ver', [
            'title'    => 'Mensagem #' . $id,
            'mensagem' => $mensagem,
        ]);
    }

    public function updateStatus(): void
    {
        if (!Csrf::check($_POST['csrf'] ?? null)) {
            Flash::set('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/mensagens');
        }

        $id = (int)($_POST['id'] ?? 0);
        $status = (string)($_POST['status'] ?? '');
        $permitidos = ['aberta', 'respondida', 'arquivada'];
        if ($id <= 0 || !in_array($status, $permitidos, true)) {
            Flash::set('error', 'Dados inválidos.');
            $this->redirect('/admin/mensagens');
        }

        $this->dao->atualizarStatus($id, $status);
        Flash::set('success', 'Status atualizado.');
        $this->redirect('/admin/mensagens');
    }

    public function responder(): void
    {
        if (!Csrf::check($_POST['csrf'] ?? null)) {
            Flash::set('error', 'Sessão expirada.');
            $this->redirect('/admin/mensagens');
        }

        $id = (int)($_POST['id'] ?? 0);
        $resposta = trim((string)($_POST['resposta'] ?? ''));
        if ($id <= 0 || $resposta === '') {
            Flash::set('error', 'Informe uma resposta.');
            $this->redirect('/admin/mensagens');
        }

        $this->dao->registrarResposta($id, $resposta);
        Flash::set('success', 'Resposta registrada.');
        $this->redirect('/admin/mensagens');
    }

    private function redirect(string $path): void
    {
        header('Location: ' . Url::to($path), true, 303);
        exit;
    }
}