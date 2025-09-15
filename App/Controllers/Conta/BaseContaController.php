<?php declare(strict_types=1);

namespace App\Controllers\Conta;

use App\Core\Auth;
use App\Core\Controller;

abstract class BaseContaController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!Auth::isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }    
}
