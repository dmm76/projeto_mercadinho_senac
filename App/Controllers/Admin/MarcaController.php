<?php declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Csrf; use App\Core\Flash; use App\Core\Url;
use App\DAO\Database; use App\DAO\MarcaDAO;

final class MarcaController extends BaseAdminController {
    public function index(): void {
        $dao=new MarcaDAO(Database::getConnection());
        $this->render('admin/marcas/index',['title'=>'Marcas','marcas'=>$dao->all()]);
    }
    public function create(): void { $this->render('admin/marcas/form',['title'=>'Nova Marca','marca'=>null]); }
    public function store(): void {
        if(!Csrf::check($_POST['csrf']??null)){Flash::set('error','Token inválido'); header('Location:'.Url::to('/admin/marcas')); exit;}
        $nome=trim($_POST['nome']??''); if($nome===''){Flash::set('error','Informe o nome'); header('Location:'.Url::to('/admin/marcas/criar')); exit;}
        (new MarcaDAO(Database::getConnection()))->create($nome);
        Flash::set('success','Marca criada!'); header('Location:'.Url::to('/admin/marcas')); exit;
    }
    public function edit(): void {
        $id=(int)($_GET['id']??0); $dao=new MarcaDAO(Database::getConnection()); $m=$dao->find($id);
        if(!$m){Flash::set('error','Marca não encontrada'); header('Location:'.Url::to('/admin/marcas')); exit;}
        $this->render('admin/marcas/form',['title'=>'Editar Marca','marca'=>$m]);
    }
    public function update(): void {
        if(!Csrf::check($_POST['csrf']??null)){Flash::set('error','Token inválido'); header('Location:'.Url::to('/admin/marcas')); exit;}
        $id=(int)($_POST['id']??0); $nome=trim($_POST['nome']??'');
        if($id<=0 || $nome===''){Flash::set('error','Dados inválidos'); header('Location:'.Url::to('/admin/marcas')); exit;}
        (new MarcaDAO(Database::getConnection()))->update($id,$nome);
        Flash::set('success','Marca atualizada!'); header('Location:'.Url::to('/admin/marcas')); exit;
    }
    public function destroy(): void {
        if(!Csrf::check($_POST['csrf']??null)){Flash::set('error','Token inválido'); header('Location:'.Url::to('/admin/marcas')); exit;}
        $id=(int)($_POST['id']??0); if($id>0){ (new MarcaDAO(Database::getConnection()))->delete($id); Flash::set('success','Marca excluída!'); }
        header('Location:'.Url::to('/admin/marcas')); exit;
    }
}
