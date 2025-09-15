<?php declare(strict_types=1);
namespace App\DAO;

use App\Model\Unidade;
use PDO;

final class UnidadeDAO
{
    public function __construct(private PDO $pdo) {}

    /** @return Unidade[] */
    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM unidade ORDER BY sigla');
        if ($stmt === false) { return []; }
        /** @var array<int, array{id:int|string,sigla:string,descricao?:string|null}> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn(array $r) => new Unidade((int)$r['id'], (string)$r['sigla'], $r['descricao'] ?? null), $rows);
    }

    public function find(int $id): ?Unidade
    {
        $st = $this->pdo->prepare('SELECT * FROM unidade WHERE id=:id');
        $st->execute([':id'=>$id]);
        /** @var array{id:int|string,sigla:string,descricao?:string|null}|false $r */
        $r = $st->fetch(PDO::FETCH_ASSOC);
        return $r === false ? null : new Unidade((int)$r['id'], (string)$r['sigla'], $r['descricao'] ?? null);
    }

    public function create(string $sigla, ?string $descricao): int
    {
        $st = $this->pdo->prepare('INSERT INTO unidade (sigla, descricao) VALUES (:s,:d)');
        $st->execute([':s'=>$sigla, ':d'=>$descricao]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $sigla, ?string $descricao): void
    {
        $st = $this->pdo->prepare('UPDATE unidade SET sigla=:s, descricao=:d WHERE id=:id');
        $st->execute([':s'=>$sigla, ':d'=>$descricao, ':id'=>$id]);
    }

    public function delete(int $id): void
    {
        $st = $this->pdo->prepare('DELETE FROM unidade WHERE id=:id');
        $st->execute([':id'=>$id]);
    }
}
