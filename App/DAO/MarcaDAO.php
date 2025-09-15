<?php declare(strict_types=1);
namespace App\DAO;

use App\Model\Marca;
use PDO;

final class MarcaDAO
{
    public function __construct(private PDO $pdo) {}

    /** @return Marca[] */
    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM marca ORDER BY nome');
        if ($stmt === false) { return []; }
        /** @var array<int, array{id:int|string,nome:string}> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn(array $r) => new Marca((int)$r['id'], (string)$r['nome']), $rows);
    }

    public function find(int $id): ?Marca
    {
        $st = $this->pdo->prepare('SELECT * FROM marca WHERE id=:id');
        $st->execute([':id'=>$id]);
        /** @var array{id:int|string,nome:string}|false $r */
        $r = $st->fetch(PDO::FETCH_ASSOC);
        return $r === false ? null : new Marca((int)$r['id'], (string)$r['nome']);
    }

    public function create(string $nome): int
    {
        $st = $this->pdo->prepare('INSERT INTO marca (nome) VALUES (:n)');
        $st->execute([':n'=>$nome]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $nome): void
    {
        $st = $this->pdo->prepare('UPDATE marca SET nome=:n WHERE id=:id');
        $st->execute([':n'=>$nome, ':id'=>$id]);
    }

    public function delete(int $id): void
    {
        $st = $this->pdo->prepare('DELETE FROM marca WHERE id=:id');
        $st->execute([':id'=>$id]);
    }
}
