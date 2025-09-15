<?php declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;

abstract class BaseAdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireAdmin();
    }
}
