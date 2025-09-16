<?php
declare(strict_types=1);

namespace App\Controllers\Site;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Url;
use App\DAO\Database;
use PDO;

final class CarrinhoController extends Controller
{
    /** Sessão do carrinho como referência para facilitar mutação */
    private function &carrinho(): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }
        return $_SESSION['carrinho'];
    }

    /** Busca dados mínimos do produto para o carrinho */
    private function buscarProdutoParaCarrinho(int $produtoId): ?array
    {
        $pdo = Database::getConnection();

        // pega o último preço cadastrado e calcula promo vigente
        $sql = "
            SELECT p.id, p.nome, p.peso_variavel, u.sigla AS unidade_sigla, p.imagem,
                   pr.preco_venda,
                   pr.preco_promocional,
                   pr.inicio_promo, pr.fim_promo,
                   CASE
                     WHEN pr.preco_promocional IS NOT NULL
                      AND (pr.inicio_promo IS NULL OR pr.inicio_promo <= NOW())
                      AND (pr.fim_promo    IS NULL OR pr.fim_promo    >= NOW())
                     THEN pr.preco_promocional
                     ELSE pr.preco_venda
                   END AS preco_atual
            FROM produto p
            JOIN unidade u ON u.id = p.unidade_id
            LEFT JOIN preco pr ON pr.produto_id = p.id
            WHERE p.id = ?
            ORDER BY pr.id DESC
            LIMIT 1
        ";

        $st = $pdo->prepare($sql);
        $st->execute([$produtoId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /** Calcula o total a partir da estrutura do carrinho da sua view */
    private function calcularTotal(array $carrinho): float
    {
        $total = 0.0;
        foreach ($carrinho as $it) {
            $p   = $it['produto'] ?? [];
            $qtd = (float)($it['quantidade'] ?? 0);
            $preco = (float)($p['preco_atual'] ?? 0);
            $total += $qtd * $preco;
        }
        return $total;
    }

    public function index(): void
    {
        $carrinho = $this->carrinho();
        $total = $this->calcularTotal($carrinho);
        $this->render('site/carrinho/index', [
            'title'    => 'Seu carrinho',
            'carrinho' => $carrinho,
            'total'    => $total,
        ]);
    }

    /** Fallback para links GET /carrinho/adicionar/{id} */
    public function adicionarGet(int $produtoId): void
    {
        $this->adicionarItem((int)$produtoId, 1.0);
        Flash::set('success', 'Produto adicionado ao carrinho.');
        header('Location: ' . Url::to('/carrinho'), true, 303);
        exit;
    }

    /** POST /carrinho/adicionar/{id} */
    public function adicionar(int $produtoId): void
    {
        if (!Csrf::check($_POST['csrf'] ?? null)) {
            Flash::set('error', 'Token inválido.');
            header('Location: ' . Url::to('/'), true, 303);
            exit;
        }
        $qtd = (float)($_POST['quantidade'] ?? 1);
        if ($qtd <= 0) $qtd = 1;

        $this->adicionarItem((int)$produtoId, $qtd);
        Flash::set('success', 'Produto adicionado ao carrinho.');
        header('Location: ' . Url::to('/carrinho'), true, 303);
        exit;
    }

    /** Lógica comum de adicionar */
    private function adicionarItem(int $produtoId, float $qtd): void
    {
        $carrinho = &$this->carrinho();
        $produto  = $this->buscarProdutoParaCarrinho($produtoId);
        if (!$produto) {
            Flash::set('error', 'Produto não encontrado.');
            return;
        }

        // passo mínimo para itens por peso (KG) = 0.001
        $step = ($produto['peso_variavel'] ?? 0) || (($produto['unidade_sigla'] ?? '') === 'KG')
            ? 0.001 : 1.0;

        // normaliza quantidade
        $qtd = max($step, round($qtd / $step) * $step);

        if (!isset($carrinho[$produtoId])) {
            $carrinho[$produtoId] = [
                'produto'    => $produto,
                'quantidade' => $qtd,
            ];
        } else {
            $carrinho[$produtoId]['quantidade'] += $qtd;
        }
    }

    /** POST /carrinho/atualizar/{id} */
    public function atualizar(int $produtoId): void
    {
        if (!Csrf::check($_POST['csrf'] ?? null)) {
            Flash::set('error', 'Token inválido.');
            header('Location: ' . Url::to('/carrinho'), true, 303);
            exit;
        }

        $carrinho = &$this->carrinho();
        if (!isset($carrinho[$produtoId])) {
            header('Location: ' . Url::to('/carrinho'), true, 303);
            exit;
        }

        $produto = $carrinho[$produtoId]['produto'];
        $step = ($produto['peso_variavel'] ?? 0) || (($produto['unidade_sigla'] ?? '') === 'KG')
            ? 0.001 : 1.0;

        $qtd = (float)($_POST['quantidade'] ?? 0);
        if ($qtd <= 0) {
            unset($carrinho[$produtoId]);
        } else {
            $qtd = max($step, round($qtd / $step) * $step);
            $carrinho[$produtoId]['quantidade'] = $qtd;
        }

        header('Location: ' . Url::to('/carrinho'), true, 303);
        exit;
    }

    /** POST /carrinho/remover/{id} */
    public function remover(int $produtoId): void
    {
        if (!Csrf::check($_POST['csrf'] ?? null)) {
            Flash::set('error', 'Token inválido.');
            header('Location: ' . Url::to('/carrinho'), true, 303);
            exit;
        }
        $carrinho = &$this->carrinho();
        unset($carrinho[$produtoId]);
        header('Location: ' . Url::to('/carrinho'), true, 303);
        exit;
    }
}
