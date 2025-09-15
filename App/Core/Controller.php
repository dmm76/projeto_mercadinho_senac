<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    /** @param array<string,mixed> $data */
    protected function render(string $template, array $data = []): void
    {
        $this->view->render($template, $data);
    }
}
