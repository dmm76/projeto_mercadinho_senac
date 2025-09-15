<?php declare(strict_types=1);
namespace App\Controllers\Site;
use App\Core\Controller;

final class PreviewController extends Controller
{
    public function home(): void
    {
        $this->render('site/preview/home', ['title' => 'Preview Home']);
    }
}
