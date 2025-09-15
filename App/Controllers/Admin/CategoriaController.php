<?php declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Url;
use App\Core\Str;
use App\DAO\CategoriaDAO;
use App\DAO\Database;

final class CategoriaController extends BaseAdminController
{
    public function index(): void
    {
        $dao = new CategoriaDAO(Database::getConnection());
        $categorias = $dao->all();
        $this->render('admin/categorias/index', ['title'=>'Categorias','categorias'=>$categorias]);
    }

    public function create(): void
    {
        $this->render('admin/categorias/form', ['title'=>'Nova Categoria','categoria'=>null]);
    }

    public function store(): void
    {
        if (!Csrf::check($_POST['csrf'] ?? null)) { Flash::set('error','Token inválido'); header('Location:'.Url::to('/admin/categorias')); exit; }
        $nome = trim($_POST['nome'] ?? '');
        $slug = trim($_POST['slug'] ?? '') ?: Str::slug($nome);
        $ativa = isset($_POST['ativa']) ? 1 : 0;
        $ordem = isset($_POST['ordem']) && $_POST['ordem'] !== '' ? (int)$_POST['ordem'] : null;

        if ($nome === '' || $slug === '') { Flash::set('error','Preencha nome/slug'); header('Location:'.Url::to('/admin/categorias/criar')); exit; }

        $dao = new CategoriaDAO(Database::getConnection());
        $dao->create($nome, $slug, $ativa, $ordem);
        Flash::set('success','Categoria criada!');
        header('Location: ' . Url::to('/admin/categorias')); exit;
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $dao = new CategoriaDAO(Database::getConnection());
        $cat = $dao->find($id);
        if (!$cat) { Flash::set('error','Categoria não encontrada'); header('Location:'.Url::to('/admin/categorias')); exit; }
        $this->render('admin/categorias/form', ['title'=>'Editar Categoria','categoria'=>$cat]);
    }

    public function update(): void
    {
        if (!Csrf::check($_POST['csrf'] ?? null)) { Flash::set('error','Token inválido'); header('Location:'.Url::to('/admin/categorias')); exit; }
        $id   = (int)($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $ativa = isset($_POST['ativa']) ? 1 : 0;
        $ordem = isset($_POST['ordem']) && $_POST['ordem'] !== '' ? (int)$_POST['ordem'] : null;

        if ($id<=0 || $nome==='' || $slug==='') { Flash::set('error','Dados inválidos'); header('Location:'.Url::to('/admin/categorias')); exit; }

        $dao = new CategoriaDAO(Database::getConnection());
        $dao->update($id, $nome, $slug, $ativa, $ordem);
        Flash::set('success','Categoria atualizada!');
        header('Location: ' . Url::to('/admin/categorias')); exit;
    }

    public function destroy(): void
    {
        if (!Csrf::check($_POST['csrf'] ?? null)) { Flash::set('error','Token inválido'); header('Location:'.Url::to('/admin/categorias')); exit; }
        $id = (int)($_POST['id'] ?? 0);
        if ($id>0) {
            $dao = new CategoriaDAO(Database::getConnection());
            $dao->delete($id);
            Flash::set('success','Categoria excluída!');
        }
        header('Location: ' . Url::to('/admin/categorias')); exit;
    }
}
