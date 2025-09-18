<?php declare(strict_types=1);

namespace App\Models;

use App\DAO\Database;
use PDO;

final class Produto
{
    public function __construct(
        public ?int $id,
        public string $nome,
        public string $sku,
        public ?string $ean,
        public int $categoriaId,
        public ?int $marcaId,
        public int $unidadeId,
        public ?string $descricao,
        public ?string $imagem,
        public int $ativo = 1,
        public int $pesoVariavel = 0
    ) {}

    /**
     * @return array{
     *     items: array<int, array<string,mixed>>,
     *     pagination: array{total:int,page:int,pages:int,per_page:int,has_previous:bool,has_next:bool}
     * }
     */
    public static function buscarParaLoja(array $params): array
    {
        $pdo = Database::getConnection();

        $perPage = (int)($params['per_page'] ?? 12);
        if ($perPage < 1) {
            $perPage = 12;
        }
        if ($perPage > 48) {
            $perPage = 48;
        }

        $page = (int)($params['page'] ?? 1);
        if ($page < 1) {
            $page = 1;
        }

        $ordem = (string)($params['ordem'] ?? 'novidades');
        $ordemPermitidas = ['novidades', 'nome', 'preco_asc', 'preco_desc'];
        if (!in_array($ordem, $ordemPermitidas, true)) {
            $ordem = 'novidades';
        }

        $q = trim((string)($params['q'] ?? ''));
        $clienteId = isset($params['cliente_id']) ? (int)$params['cliente_id'] : null;
        if ($clienteId !== null && $clienteId <= 0) {
            $clienteId = null;
        }

        $somenteFavoritos = !empty($params['somente_favoritos']) && $clienteId !== null;
        $favoritosPrimeiro = !empty($params['favoritos_primeiro']) && $clienteId !== null && !$somenteFavoritos;

        if ($somenteFavoritos && $clienteId === null) {
            return self::emptyCatalog($perPage);
        }

        $where = ['p.ativo = 1'];
        $bindings = [];
        if ($q !== '') {
            $where[] = '(p.nome LIKE :buscaNome OR p.descricao LIKE :buscaDescricao OR p.sku LIKE :buscaSku)';
            $valorBusca = '%' . $q . '%';
            $bindings[':buscaNome'] = $valorBusca;
            $bindings[':buscaDescricao'] = $valorBusca;
            $bindings[':buscaSku'] = $valorBusca;
        }

        $countSql = 'SELECT COUNT(*) FROM produto p';
        $countBindings = $bindings;
        if ($somenteFavoritos && $clienteId !== null) {
            $countSql .= ' JOIN cliente_favorito cf ON cf.produto_id = p.id AND cf.cliente_id = :clienteIdCount';
            $countBindings[':clienteIdCount'] = $clienteId;
        }
        if ($where) {
            $countSql .= ' WHERE ' . implode(' AND ', $where);
        }
        $countStmt = $pdo->prepare($countSql);
        foreach ($countBindings as $key => $value) {
            $countStmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        $pages = $total > 0 ? (int)ceil($total / $perPage) : 1;
        if ($page > $pages) {
            $page = $pages;
        }
        if ($page < 1) {
            $page = 1;
        }
        $offset = ($page - 1) * $perPage;

        $select = [
            'p.id',
            'p.nome',
            'p.descricao',
            'p.imagem',
            'p.sku',
            'p.peso_variavel',
            'u.sigla AS unidade_sigla',
            'COALESCE(e.quantidade, 0) AS estoque_qtd',
            'pr.preco_venda',
            'pr.preco_promocional',
            'pr.inicio_promo',
            'pr.fim_promo',
            'CASE
                WHEN pr.preco_promocional IS NOT NULL
                 AND (pr.inicio_promo IS NULL OR pr.inicio_promo <= NOW())
                 AND (pr.fim_promo    IS NULL OR pr.fim_promo    >= NOW())
                THEN pr.preco_promocional
                ELSE pr.preco_venda
             END AS preco_atual'
        ];

        $joins = [
            'JOIN unidade u ON u.id = p.unidade_id',
            'LEFT JOIN estoque e ON e.produto_id = p.id',
            'LEFT JOIN (
                SELECT pr1.produto_id, pr1.preco_venda, pr1.preco_promocional, pr1.inicio_promo, pr1.fim_promo
                FROM preco pr1
                JOIN (
                    SELECT produto_id, MAX(criado_em) AS maxc
                    FROM preco
                    GROUP BY produto_id
                ) ult ON ult.produto_id = pr1.produto_id AND ult.maxc = pr1.criado_em
            ) pr ON pr.produto_id = p.id'
        ];

        if ($clienteId !== null) {
            $joinType = $somenteFavoritos ? 'JOIN' : 'LEFT JOIN';
            $joins[] = $joinType . ' cliente_favorito cf ON cf.produto_id = p.id AND cf.cliente_id = :clienteId';
            $bindings[':clienteId'] = $clienteId;
            $select[] = $somenteFavoritos ? '1 AS is_favorito' : 'CASE WHEN cf.cliente_id IS NULL THEN 0 ELSE 1 END AS is_favorito';
        } else {
            $select[] = '0 AS is_favorito';
        }

        $orderParts = [];
        if ($clienteId !== null && !$somenteFavoritos && $favoritosPrimeiro) {
            $orderParts[] = 'CASE WHEN cf.cliente_id IS NULL THEN 1 ELSE 0 END';
        }
        switch ($ordem) {
            case 'nome':
                $orderParts[] = 'p.nome ASC';
                break;
            case 'preco_asc':
                $orderParts[] = 'preco_atual ASC';
                break;
            case 'preco_desc':
                $orderParts[] = 'preco_atual DESC';
                break;
            default:
                $orderParts[] = 'p.criado_em DESC';
                break;
        }
        $orderParts[] = 'p.id DESC';

        $sql = 'SELECT ' . implode(', ', $select) . ' FROM produto p ' . implode(' ', $joins);
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY ' . implode(', ', $orderParts) . ' LIMIT :limit OFFSET :offset';

        $stmt = $pdo->prepare($sql);
        foreach ($bindings as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return [
            'items' => $items,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'pages' => $total > 0 ? $pages : 0,
                'per_page' => $perPage,
                'has_previous' => $page > 1,
                'has_next' => $total > 0 ? $page < $pages : false,
            ],
        ];
    }

    public static function todosAtivos(): array
    {
        $result = self::buscarParaLoja([
            'per_page' => 9999,
            'page' => 1,
            'favoritos_primeiro' => false,
        ]);
        return $result['items'];
    }

    public static function encontrarAtivo(int $id): ?array
    {
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

        $pdo = Database::getConnection();
        $st = $pdo->prepare($sql);
        $st->execute([$id, $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    private static function emptyCatalog(int $perPage): array
    {
        return [
            'items' => [],
            'pagination' => [
                'total' => 0,
                'page' => 1,
                'pages' => 0,
                'per_page' => $perPage,
                'has_previous' => false,
                'has_next' => false,
            ],
        ];
    }
}

