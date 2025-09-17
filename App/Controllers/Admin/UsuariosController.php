<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\DAO\Database;
use PDO;

final class UsuariosController extends BaseAdminController
{
    public function index(): void
    {
        $filters = [
            'q'      => trim((string)($_GET['q'] ?? '')), // nome ou e-mail
            'perfil' => trim((string)($_GET['perfil'] ?? '')),
            'status' => trim((string)($_GET['status'] ?? '')),
        ];

        $pdo = Database::getConnection();

        $sql = 'SELECT id, nome, email, perfil, ativo, criado_em FROM usuario WHERE 1=1';
        $params = [];

        if ($filters['q'] !== '') {
            $sql .= ' AND (nome LIKE :q OR email LIKE :q)';
            $params[':q'] = "%{$filters['q']}%";
        }

        if ($filters['perfil'] !== '') {
            $sql .= ' AND perfil = :perfil';
            $params[':perfil'] = $filters['perfil'];
        }

        if ($filters['status'] === 'ativo') {
            $sql .= ' AND ativo = 1';
        } elseif ($filters['status'] === 'inativo') {
            $sql .= ' AND ativo = 0';
        }

        $sql .= ' ORDER BY criado_em DESC LIMIT 100';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $this->render('admin/usuarios/index', [
            'title'    => 'Usuários',
            'usuarios' => $usuarios,
            'filters'  => $filters,
        ]);
    }
}
