<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\DAO\Database;
use PDO;

final class ConfiguracoesController extends BaseAdminController
{
    public function index(): void
    {
        $pdo = Database::getConnection();

        $config = [
            'nome_loja'     => 'Mercadinho Borba Gato',
            'email_contato' => 'contato@mercadinhobg.com.br',
            'telefone'      => '(44) 3259-1533',
            'horario'       => 'Segunda a Sábado – 08h às 20h',
            'banner_msg'    => 'Entregamos em Borba Gato e região. Peça até as 19h!',
            'checkout_msg'  => 'Obrigado pela preferência! Vamos confirmar a entrega pelo WhatsApp.',
            'pix_chave'     => 'mercadinho@pix.com.br',
            'pedido_minimo' => '30.00',
        ];

        try {
            $stmt = $pdo->query('SELECT chave, valor FROM configuracao');
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $config[$row['chave']] = $row['valor'];
            }
        } catch (\PDOException $e) {
        }

        $this->render('admin/configuracoes/index', [
            'title'  => 'Configurações',
            'config' => $config,
        ]);
    }
}
