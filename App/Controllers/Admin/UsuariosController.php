<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Url;
use App\DAO\Database;
use App\DAO\UsuarioDAO;

final class UsuariosController extends BaseAdminController
{
    private UsuarioDAO $dao;

    public function __construct()
    {
        parent::__construct();
        $this->dao = new UsuarioDAO(Database::getConnection());
    }

    public function index(): void
    {
        $filters = [
            'q'      => trim((string)($_GET['q'] ?? '')),
            'perfil' => trim((string)($_GET['perfil'] ?? '')),
            'status' => trim((string)($_GET['status'] ?? '')),
        ];

        $usuarios = $this->dao->search($filters);

        $this->render('admin/usuarios/index', [
            'title'    => 'Usuários',
            'usuarios' => $usuarios,
            'filters'  => $filters,
        ]);
    }

    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            Flash::set('error', 'Usuário inválido.');
            $this->redirect('/admin/usuarios');
        }

        $usuario = $this->dao->find($id);
        if ($usuario === null) {
            Flash::set('error', 'Usuário não encontrado.');
            $this->redirect('/admin/usuarios');
        }

        $this->render('admin/usuarios/ver', [
            'title'   => 'Detalhes do usuário',
            'usuario' => $usuario,
        ]);
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            Flash::set('error', 'Usuário inválido.');
            $this->redirect('/admin/usuarios');
        }

        $usuario = $this->dao->find($id);
        if ($usuario === null) {
            Flash::set('error', 'Usuário não encontrado.');
            $this->redirect('/admin/usuarios');
        }

        $this->render('admin/usuarios/editar', [
            'title'   => 'Editar usuário',
            'usuario' => $usuario,
        ]);
    }

    public function update(): void
    {
        if (!Csrf::check($_POST['csrf'] ?? null)) {
            Flash::set('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/usuarios');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            Flash::set('error', 'Usuário inválido.');
            $this->redirect('/admin/usuarios');
        }

        $nome   = trim((string)($_POST['nome'] ?? ''));
        $email  = trim((string)($_POST['email'] ?? ''));
        $perfil = trim((string)($_POST['perfil'] ?? 'cliente'));
        $ativo  = isset($_POST['ativo']) ? true : false;

        $perfisPermitidos = ['admin', 'gerente', 'operador', 'cliente'];
        if (!in_array($perfil, $perfisPermitidos, true)) {
            Flash::set('error', 'Perfil inválido.');
            $this->redirect('/admin/usuarios/editar?id=' . $id);
        }
        if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flash::set('error', 'Informe nome e e-mail válidos.');
            $this->redirect('/admin/usuarios/editar?id=' . $id);
        }

        try {
            $this->dao->updateBasico($id, $nome, $email, $perfil, $ativo);
            Flash::set('success', 'Usuário atualizado com sucesso.');
        } catch (\Throwable $e) {
            Flash::set('error', $e->getMessage());
            $this->redirect('/admin/usuarios/editar?id=' . $id);
        }

        $this->redirect('/admin/usuarios');
    }

    public function toggleStatus(): void
    {
        if (!Csrf::check($_POST['csrf'] ?? null)) {
            Flash::set('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/usuarios');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            Flash::set('error', 'Usuário inválido.');
            $this->redirect('/admin/usuarios');
        }

        $acao = ($_POST['acao'] ?? '') === 'ativar';
        $this->dao->setAtivo($id, $acao);
        Flash::set('success', $acao ? 'Usuário ativado.' : 'Usuário desativado.');
        $this->redirect('/admin/usuarios');
    }

    private function redirect(string $path): void
    {
        header('Location: ' . Url::to($path), true, 303);
        exit;
    }
}