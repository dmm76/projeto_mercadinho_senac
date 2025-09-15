<?php declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Url;
use App\Core\Upload;
use App\DAO\Database;
use App\DAO\ProdutoDAO;
use App\DAO\CategoriaDAO;
use App\DAO\MarcaDAO;
use App\DAO\UnidadeDAO;
use App\DAO\PrecoDAO;
use App\DAO\EstoqueDAO;
use App\Model\Produto;

final class ProdutoController extends BaseAdminController
{
    public function index(): void
    {
        $dao = new ProdutoDAO(Database::getConnection());
        $produtos = $dao->listWithJoins();
        $this->render('admin/produtos/index', ['title'=>'Produtos','produtos'=>$produtos]);
    }

    public function create(): void
    {
        $pdo = Database::getConnection();
        $this->render('admin/produtos/form', [
            'title'=>'Novo Produto',
            'produto'=>null,
            'categorias'=>(new CategoriaDAO($pdo))->all(),
            'marcas'=>(new MarcaDAO($pdo))->all(),
            'unidades'=>(new UnidadeDAO($pdo))->all(),
            'estoque'=>null,
        ]);
    }

    public function store(): void
    {
        if (!Csrf::check($_POST['csrf'] ?? null)) { Flash::set('error','Token inválido'); header('Location:'.Url::to('/admin/produtos')); exit; }

        $nome  = trim($_POST['nome'] ?? '');
        $sku   = trim($_POST['sku'] ?? '');
        $ean   = trim($_POST['ean'] ?? '') ?: null;
        $catId = (int)($_POST['categoria_id'] ?? 0);
        $marId = ($_POST['marca_id'] ?? '') === '' ? null : (int)$_POST['marca_id'];
        $uniId = (int)($_POST['unidade_id'] ?? 0);
        $desc  = trim($_POST['descricao'] ?? '') ?: null;
        $ativo = isset($_POST['ativo']) ? 1 : 0;
        $pesoV = isset($_POST['peso_variavel']) ? 1 : 0;

        $precoVenda = (float)($_POST['preco_venda'] ?? 0);
        $promo      = ($_POST['preco_promocional'] ?? '') === '' ? null : (float)$_POST['preco_promocional'];
        $ini        = ($_POST['inicio_promo'] ?? '') ?: null;
        $fim        = ($_POST['fim_promo'] ?? '') ?: null;

        $qtdIni = ($_POST['estoque_qtd'] ?? '') === '' ? 0.0 : (float)$_POST['estoque_qtd'];
        $minimo = ($_POST['estoque_min'] ?? '') === '' ? 0.0 : (float)$_POST['estoque_min'];

        if ($nome==='' || $sku==='' || $catId<=0 || $uniId<=0 || $precoVenda<=0) {
            Flash::set('error','Preencha os campos obrigatórios (nome, sku, categoria, unidade, preço).');
            header('Location:'.Url::to('/admin/produtos/criar')); exit;
        }

        $pdo = Database::getConnection();
        $produto = new Produto(null, $nome, $sku, $ean, $catId, $marId, $uniId, $desc, null, $ativo, $pesoV);

        $id = Database::transaction(function() use ($pdo, $produto, $precoVenda, $promo, $ini, $fim, $qtdIni, $minimo) {
            $pdao = new ProdutoDAO($pdo);
            $id = $pdao->create($produto);

            // upload imagem (opcional)
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $name = Upload::image($_FILES['imagem'], __DIR__ . '/../../../public/uploads/produtos');
                $produto->id = $id; $produto->imagem = 'uploads/produtos/' . $name;
                $pdao->update($produto);
            }

            // preco inicial
            (new PrecoDAO($pdo))->create($id, $precoVenda, $promo, $ini, $fim);

            // estoque inicial
            (new EstoqueDAO($pdo))->createInit($id, $qtdIni, $minimo);

            return $id;
        });

        Flash::set('success','Produto criado! (#'.$id.')');
        header('Location:'.Url::to('/admin/produtos')); exit;
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $pdo = Database::getConnection();
        $p = (new ProdutoDAO($pdo))->find($id);
        if (!$p) { Flash::set('error','Produto não encontrado'); header('Location:'.Url::to('/admin/produtos')); exit; }

        $estoque = (new EstoqueDAO($pdo))->getByProduto($id);

        $this->render('admin/produtos/form', [
            'title'=>'Editar Produto',
            'produto'=>$p,
            'categorias'=>(new CategoriaDAO($pdo))->all(),
            'marcas'=>(new MarcaDAO($pdo))->all(),
            'unidades'=>(new UnidadeDAO($pdo))->all(),
            'estoque'=>$estoque,
        ]);
    }

    public function update(): void
    {
        if (!Csrf::check($_POST['csrf'] ?? null)) { Flash::set('error','Token inválido'); header('Location:'.Url::to('/admin/produtos')); exit; }

        $id    = (int)($_POST['id'] ?? 0);
        $nome  = trim($_POST['nome'] ?? '');
        $sku   = trim($_POST['sku'] ?? '');
        $ean   = trim($_POST['ean'] ?? '') ?: null;
        $catId = (int)($_POST['categoria_id'] ?? 0);
        $marId = ($_POST['marca_id'] ?? '') === '' ? null : (int)$_POST['marca_id'];
        $uniId = (int)($_POST['unidade_id'] ?? 0);
        $desc  = trim($_POST['descricao'] ?? '') ?: null;
        $ativo = isset($_POST['ativo']) ? 1 : 0;
        $pesoV = isset($_POST['peso_variavel']) ? 1 : 0;

        // preço (se informado, cria novo registro em preco)
        $precoVenda = ($_POST['preco_venda'] ?? '') === '' ? null : (float)$_POST['preco_venda'];
        $promo      = ($_POST['preco_promocional'] ?? '') === '' ? null : (float)$_POST['preco_promocional'];
        $ini        = ($_POST['inicio_promo'] ?? '') ?: null;
        $fim        = ($_POST['fim_promo'] ?? '') ?: null;

        $qtd = ($_POST['estoque_qtd'] ?? '') === '' ? null : (float)$_POST['estoque_qtd'];
        $min = ($_POST['estoque_min'] ?? '') === '' ? null : (float)$_POST['estoque_min'];

        if ($id<=0 || $nome==='' || $sku==='' || $catId<=0 || $uniId<=0) {
            Flash::set('error','Dados inválidos.'); header('Location:'.Url::to('/admin/produtos')); exit;
        }

        $pdo = Database::getConnection();
        Database::transaction(function() use ($pdo,$id,$nome,$sku,$ean,$catId,$marId,$uniId,$desc,$ativo,$pesoV,$precoVenda,$promo,$ini,$fim,$qtd,$min) {

            $pdao = new ProdutoDAO($pdo);
            $p = $pdao->find($id);
            if (!$p) { throw new \RuntimeException('Produto não existe'); }

            $p->nome = $nome; $p->sku = $sku; $p->ean = $ean;
            $p->categoriaId = $catId; $p->marcaId = $marId; $p->unidadeId = $uniId;
            $p->descricao = $desc; $p->ativo = $ativo; $p->pesoVariavel = $pesoV;

            // upload opcional
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $name = Upload::image($_FILES['imagem'], __DIR__ . '/../../../public/uploads/produtos');
                $p->imagem = 'uploads/produtos/' . $name;
            }

            $pdao->update($p);

            if ($precoVenda !== null && $precoVenda > 0) {
                (new PrecoDAO($pdo))->create($id, $precoVenda, $promo, $ini, $fim);
            }
            if ($qtd !== null || $min !== null) {
                // garante existência
                $est = new EstoqueDAO($pdo);
                $current = $est->getByProduto($id) ?? ['quantidade'=>0.0,'minimo'=>0.0];
                $est->update($id, $qtd !== null ? $qtd : $current['quantidade'], $min !== null ? $min : $current['minimo']);
            }
        });

        Flash::set('success','Produto atualizado!');
        header('Location:'.Url::to('/admin/produtos')); exit;
    }

    public function destroy(): void
    {
        if (!Csrf::check($_POST['csrf'] ?? null)) { Flash::set('error','Token inválido'); header('Location:'.Url::to('/admin/produtos')); exit; }
        $id = (int)($_POST['id'] ?? 0);

        try {
            (new ProdutoDAO(Database::getConnection()))->delete($id);
            Flash::set('success','Produto excluído!');
        } catch (\Throwable $e) {
            Flash::set('error','Não foi possível excluir (há vínculos?).');
        }
        header('Location:'.Url::to('/admin/produtos')); exit;
    }
}
