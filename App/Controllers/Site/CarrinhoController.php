<?php
declare(strict_types=1);

namespace App\Controllers\Site;

use App\Core\Controller;
use App\Core\Url;
use App\DAO\Database;
use PDO;

class CarrinhoController extends Controller
{
    /** @return array<int, array{produto:array, quantidade:float}> */
    private function &carrinho(): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['carrinho'])) $_SESSION['carrinho'] = [];
        return $_SESSION['carrinho'];
    }

    private function redirect(string $path): void
    {
        header('Location: ' . Url::to($path), true, 302);
        exit;
    }

    public function index(): void
    {
        $carrinho = $this->carrinho();
        $total = 0.0;
        foreach ($carrinho as $it) {
            $total += (float)$it['produto']['preco_atual'] * (float)$it['quantidade'];
        }
        $this->render('site/carrinho/index', compact('carrinho', 'total'));
    }

    public function adicionar(int $id): void
    {
        $produto = $this->carregarProduto($id);
        if (!$produto) $this->redirect('/produtos');

        $step = ($produto['peso_variavel'] || $produto['unidade_sigla'] === 'KG') ? 0.001 : 1.0;
        $q = isset($_POST['quantidade']) ? (float)$_POST['quantidade'] : $step;
        if ($q <= 0) $q = $step;

        $carrinho = &$this->carrinho();
        if (!isset($carrinho[$id])) $carrinho[$id] = ['produto' => $produto, 'quantidade' => 0.0];
        $carrinho[$id]['quantidade'] = round($carrinho[$id]['quantidade'] + $q, 3);

        $this->redirect('/carrinho');
    }

    public function atualizar(int $id): void
    {
        $carrinho = &$this->carrinho();
        if (!isset($carrinho[$id])) $this->redirect('/carrinho');

        $q = (float)($_POST['quantidade'] ?? 0);
        if ($q <= 0) unset($carrinho[$id]);
        else $carrinho[$id]['quantidade'] = round($q, 3);

        $this->redirect('/carrinho');
    }

    public function remover(int $id): void
    {
        $carrinho = &$this->carrinho();
        unset($carrinho[$id]);
        $this->redirect('/carrinho');
    }

    private function carregarProduto(int $id): ?array
    {
        $pdo = Database::getConnection(); // usa seu DAO já existente
        $sql = "
            SELECT p.*,
                   u.sigla AS unidade_sigla,
                   e.quantidade AS estoque_qtd,
                   pr.preco_venda, pr.preco_promocional, pr.inicio_promo, pr.fim_promo,
                   CASE
                     WHEN pr.preco_promocional IS NOT NULL
                      AND (pr.inicio_promo IS NULL OR pr.inicio_promo <= NOW())
                      AND (pr.fim_promo    IS NULL OR pr.fim_promo    >= NOW())
                     THEN pr.preco_promocional
                     ELSE pr.preco_venda
                   END AS preco_atual
            FROM produto p
            JOIN unidade u      ON u.id = p.unidade_id
            LEFT JOIN estoque e ON e.produto_id = p.id
            LEFT JOIN (
               SELECT pr1.*
               FROM preco pr1
               WHERE pr1.produto_id = :id
               ORDER BY pr1.id DESC
               LIMIT 1
            ) pr ON pr.produto_id = p.id
            WHERE p.ativo = 1 AND p.id = :id
        ";
        $st = $pdo->prepare($sql);
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
