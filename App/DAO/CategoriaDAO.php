<?php declare(strict_types=1);
namespace App\DAO;

use App\Model\Categoria;
use PDO;

final class CategoriaDAO
{
    public function __construct(private PDO $pdo) {}

    /** @return Categoria[] */
    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM categoria ORDER BY ordem IS NULL, ordem, nome');
        if ($stmt === false) { return []; }
        /** @var array<int, array{id:int|string,nome:string,slug:string,ativa:int|string,ordem?:int|string|null}> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $out = [];
        foreach ($rows as $r) {
            $out[] = new Categoria(
                (int)$r['id'],
                (string)$r['nome'],
                (string)$r['slug'],
                (int)$r['ativa'],
                isset($r['ordem']) ? (int)$r['ordem'] : null
            );
        }
        return $out;
    }

    public function find(int $id): ?Categoria
    {
        $st = $this->pdo->prepare('SELECT * FROM categoria WHERE id=:id');
        $st->execute([':id'=>$id]);
        /** @var array{id:int|string,nome:string,slug:string,ativa:int|string,ordem?:int|string|null}|false $r */
        $r = $st->fetch(PDO::FETCH_ASSOC);
        if ($r === false) { return null; }
        return new Categoria(
            (int)$r['id'], (string)$r['nome'], (string)$r['slug'],
            (int)$r['ativa'], isset($r['ordem']) ? (int)$r['ordem'] : null
        );
    }

    public function create(string $nome, string $slug, int $ativa = 1, ?int $ordem = null): int
    {
        $st = $this->pdo->prepare('INSERT INTO categoria (nome, slug, ativa, ordem) VALUES (:n,:s,:a,:o)');
        $st->execute([':n'=>$nome, ':s'=>$slug, ':a'=>$ativa, ':o'=>$ordem]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $nome, string $slug, int $ativa, ?int $ordem): void
    {
        $st = $this->pdo->prepare('UPDATE categoria SET nome=:n, slug=:s, ativa=:a, ordem=:o WHERE id=:id');
        $st->execute([':n'=>$nome, ':s'=>$slug, ':a'=>$ativa, ':o'=>$ordem, ':id'=>$id]);
    }

    public function delete(int $id): void
    {
        $st = $this->pdo->prepare('DELETE FROM categoria WHERE id=:id');
        $st->execute([':id'=>$id]);
    }
}
