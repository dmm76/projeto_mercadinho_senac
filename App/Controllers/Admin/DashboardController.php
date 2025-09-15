<?php declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Url;
use App\DAO\Database;
use PDOStatement;

final class DashboardController extends BaseAdminController
{
    public function index(): void
    {
        $pdo = Database::getConnection();

        /** @var PDOStatement|false $s1 */
        $s1 = $pdo->query('SELECT COUNT(*) FROM produto');
        $produtos = (int) ($s1 !== false ? $s1->fetchColumn() : 0);

        /** @var PDOStatement|false $s2 */
        $s2 = $pdo->query("
            SELECT COUNT(*)
            FROM estoque e
            JOIN produto p ON p.id = e.produto_id
            WHERE e.minimo > 0 AND e.quantidade < e.minimo
        ");
        $estoqueBaixo = (int) ($s2 !== false ? $s2->fetchColumn() : 0);

        $this->render('admin/dashboard/index', [
            'title' => 'Dashboard Admin',
            'metrics' => [
                'produtos' => $produtos,
                'estoque_baixo' => $estoqueBaixo,
            ],
            'links' => [
                'categorias' => Url::to('/admin/categorias'),
                'marcas'     => Url::to('/admin/marcas'),
                'unidades'   => Url::to('/admin/unidades'),
                'produtos'   => Url::to('/admin/produtos'),
            ],
        ]);
    }
}
