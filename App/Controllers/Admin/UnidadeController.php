<?php declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Csrf; use App\Core\Flash; use App\Core\Url;
use App\DAO\Database; use App\DAO\UnidadeDAO;

final class UnidadeController extends BaseAdminController {
    public function index(): void {
        $dao=new UnidadeDAO(Database::getConnection());
        $this->render('admin/unidades/index',['title'=>'Unidades','unidades'=>$dao->all()]);
    }
    public function create(): void { $this->render('admin/unidades/form',['title'=>'Nova Unidade','unidade'=>null]); }
    public function store(): void {
        if(!Csrf::check($_POST['csrf']??null)){Flash::set('error','Token inválido'); header('Location:'.Url::to('/admin/unidades')); exit;}
        $sigla=strtoupper(trim($_POST['sigla']??'')); $desc=trim($_POST['descricao']??'') ?: null;
        if($sigla===''){Flash::set('error','Informe a sigla'); header('Location:'.Url::to('/admin/unidades/criar')); exit;}
        (new UnidadeDAO(Database::getConnection()))->create($sigla,$desc);
        Flash::set('success','Unidade criada!'); header('Location:'.Url::to('/admin/unidades')); exit;
    }
    public function edit(): void {
        $id=(int)($_GET['id']??0); $dao=new UnidadeDAO(Database::getConnection()); $u=$dao->find($id);
        if(!$u){Flash::set('error','Unidade não encontrada'); header('Location:'.Url::to('/admin/unidades')); exit;}
        $this->render('admin/unidades/form',['title'=>'Editar Unidade','unidade'=>$u]);
    }
    public function update(): void {
        if(!Csrf::check($_POST['csrf']??null)){Flash::set('error','Token inválido'); header('Location:'.Url::to('/admin/unidades')); exit;}
        $id=(int)($_POST['id']??0); $sigla=strtoupper(trim($_POST['sigla']??'')); $desc=trim($_POST['descricao']??'') ?: null;
        if($id<=0 || $sigla===''){Flash::set('error','Dados inválidos'); header('Location:'.Url::to('/admin/unidades')); exit;}
        (new UnidadeDAO(Database::getConnection()))->update($id,$sigla,$desc);
        Flash::set('success','Unidade atualizada!'); header('Location:'.Url::to('/admin/unidades')); exit;
    }
    public function destroy(): void {
        if(!Csrf::check($_POST['csrf']??null)){Flash::set('error','Token inválido'); header('Location:'.Url::to('/admin/unidades')); exit;}
        $id=(int)($_POST['id']??0); if($id>0){ (new UnidadeDAO(Database::getConnection()))->delete($id); Flash::set('success','Unidade excluída!'); }
        header('Location:'.Url::to('/admin/unidades')); exit;
    }
}
