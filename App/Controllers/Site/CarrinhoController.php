<?php declare(strict_types=1);

namespace App\Controllers\Site;

final class CarrinhoController
{
    public function index(): void
    {
        $title = 'Seu carrinho';
        $count = $_SESSION['cart_count'] ?? 0;
        require __DIR__ . '/../../Views/site/home/carrinho.php';
    }
}
