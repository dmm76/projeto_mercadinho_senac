<?php declare(strict_types=1);

use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\CategoriaController;
use App\Controllers\Admin\MarcaController;
use App\Controllers\Admin\UnidadeController;
use App\Controllers\Admin\ProdutoController;

$router->get('/admin', [DashboardController::class, 'index']);

// Categorias
$router->get('/admin/categorias', [CategoriaController::class, 'index']);
$router->get('/admin/categorias/criar', [CategoriaController::class, 'create']);
$router->post('/admin/categorias/criar', [CategoriaController::class, 'store']);
$router->get('/admin/categorias/editar', [CategoriaController::class, 'edit']);     // ?id=#
$router->post('/admin/categorias/editar', [CategoriaController::class, 'update']);
$router->post('/admin/categorias/excluir', [CategoriaController::class, 'destroy']);

// Marcas
$router->get('/admin/marcas', [MarcaController::class, 'index']);
$router->get('/admin/marcas/criar', [MarcaController::class, 'create']);
$router->post('/admin/marcas/criar', [MarcaController::class, 'store']);
$router->get('/admin/marcas/editar', [MarcaController::class, 'edit']);            // ?id=#
$router->post('/admin/marcas/editar', [MarcaController::class, 'update']);
$router->post('/admin/marcas/excluir', [MarcaController::class, 'destroy']);

// Unidades
$router->get('/admin/unidades', [UnidadeController::class, 'index']);
$router->get('/admin/unidades/criar', [UnidadeController::class, 'create']);
$router->post('/admin/unidades/criar', [UnidadeController::class, 'store']);
$router->get('/admin/unidades/editar', [UnidadeController::class, 'edit']);        // ?id=#
$router->post('/admin/unidades/editar', [UnidadeController::class, 'update']);
$router->post('/admin/unidades/excluir', [UnidadeController::class, 'destroy']);

// Produtos
$router->get('/admin/produtos', [ProdutoController::class, 'index']);
$router->get('/admin/produtos/criar', [ProdutoController::class, 'create']);
$router->post('/admin/produtos/criar', [ProdutoController::class, 'store']);
$router->get('/admin/produtos/editar', [ProdutoController::class, 'edit']);        // ?id=#
$router->post('/admin/produtos/editar', [ProdutoController::class, 'update']);
$router->post('/admin/produtos/excluir', [ProdutoController::class, 'destroy']);   // id hidden
