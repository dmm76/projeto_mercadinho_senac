<?php declare(strict_types=1);

namespace App\Controllers\Site;

final class HomeController
{
    public function index(): void
    {
        $title = 'Mercadinho';
        require __DIR__ . '/../../Views/site/home/index.php';
    }
}
