<?php declare(strict_types=1);
namespace App\Models;

use App\Core\BD;
use PDO;

final class Produto
{
    public function __construct(
        public ?int   $id,
        public string $nome,
        public string $sku,
        public ?string $ean,
        public int    $categoriaId,
        public ?int   $marcaId,
        public int    $unidadeId,
        public ?string $descricao,
        public ?string $imagem,
        public int    $ativo = 1,
        public int    $pesoVariavel = 0
    ) {}

    public static function todosAtivos(): array {
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
           JOIN (
             SELECT produto_id, MAX(criado_em) AS maxc
             FROM preco
             GROUP BY produto_id
           ) ult ON ult.produto_id = pr1.produto_id AND ult.maxc = pr1.criado_em
        ) pr ON pr.produto_id = p.id
        WHERE p.ativo = 1
        ORDER BY p.id DESC";
        return BD::conn()->query($sql)->fetchAll();
    }

    public static function encontrarAtivo(int $id): ?array {
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
           WHERE pr1.produto_id = ?
           ORDER BY pr1.criado_em DESC
           LIMIT 1
        ) pr ON pr.produto_id = p.id
        WHERE p.ativo = 1 AND p.id = ?";
        $st = BD::conn()->prepare($sql);
        $st->execute([$id, $id]);
        $row = $st->fetch();
        return $row ?: null;
    }

}
