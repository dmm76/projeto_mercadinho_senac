<?php declare(strict_types=1);
namespace App\DAO;

use App\Model\Produto;
use PDO;

final class ProdutoDAO
{
    public function __construct(private PDO $pdo) {}

    /** @return array<int, array<string,mixed>> */
    public function listWithJoins(): array
    {
        $sql = "SELECT p.*,
                   c.nome AS categoria, m.nome AS marca, u.sigla AS unidade,
                   (SELECT pr.preco_venda FROM preco pr WHERE pr.produto_id=p.id ORDER BY pr.id DESC LIMIT 1) AS preco_atual,
                   (SELECT e.quantidade FROM estoque e WHERE e.produto_id=p.id LIMIT 1) AS estoque_qtd
                FROM produto p
                LEFT JOIN categoria c ON c.id=p.categoria_id
                LEFT JOIN marca m ON m.id=p.marca_id
                JOIN unidade u ON u.id=p.unidade_id
                ORDER BY p.nome";
        $stmt = $this->pdo->query($sql);
        if ($stmt === false) { return []; }
        /** @var array<int, array<string,mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    public function find(int $id): ?Produto
    {
        $st = $this->pdo->prepare('SELECT * FROM produto WHERE id=:id');
        $st->execute([':id'=>$id]);
        /** @var array{
         *   id:int|string,nome:string,sku:string,ean?:string|null,
         *   categoria_id:int|string,marca_id?:int|string|null,unidade_id:int|string,
         *   descricao?:string|null,imagem?:string|null,ativo:int|string,peso_variavel:int|string
         * }|false $r */
        $r = $st->fetch(PDO::FETCH_ASSOC);
        if ($r === false) { return null; }

        return new Produto(
            (int)$r['id'],
            (string)$r['nome'],
            (string)$r['sku'],
            $r['ean'] ?? null,
            (int)$r['categoria_id'],
            isset($r['marca_id']) ? (int)$r['marca_id'] : null,
            (int)$r['unidade_id'],
            $r['descricao'] ?? null,
            $r['imagem'] ?? null,
            (int)$r['ativo'],
            (int)$r['peso_variavel']
        );
    }

    public function create(Produto $p): int
    {
        $st = $this->pdo->prepare('INSERT INTO produto (nome,sku,ean,categoria_id,marca_id,unidade_id,descricao,imagem,ativo,peso_variavel,criado_em)
                                   VALUES (:n,:sku,:ean,:cat,:mar,:uni,:d,:img,:a,:pv,NOW())');
        $st->execute([
            ':n'=>$p->nome, ':sku'=>$p->sku, ':ean'=>$p->ean,
            ':cat'=>$p->categoriaId, ':mar'=>$p->marcaId, ':uni'=>$p->unidadeId,
            ':d'=>$p->descricao, ':img'=>$p->imagem, ':a'=>$p->ativo, ':pv'=>$p->pesoVariavel
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(Produto $p): void
    {
        $st = $this->pdo->prepare(
            'UPDATE produto SET nome=:n, sku=:sku, ean=:ean, categoria_id=:cat, marca_id=:mar, unidade_id=:uni,
             descricao=:d, imagem=:img, ativo=:a, peso_variavel=:pv WHERE id=:id'
        );
        $st->execute([
            ':n'=>$p->nome, ':sku'=>$p->sku, ':ean'=>$p->ean,
            ':cat'=>$p->categoriaId, ':mar'=>$p->marcaId, ':uni'=>$p->unidadeId,
            ':d'=>$p->descricao, ':img'=>$p->imagem, ':a'=>$p->ativo, ':pv'=>$p->pesoVariavel,
            ':id'=>$p->id
        ]);
    }

    public function delete(int $id): void
    {
        $st = $this->pdo->prepare('DELETE FROM produto WHERE id=:id');
        $st->execute([':id'=>$id]);
    }
}
