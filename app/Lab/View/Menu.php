<?php

namespace App\Lab\View;

use App\Lab\Controller\DirectorController;
use App\Lab\Controller\GenreController;
use App\Lab\Controller\MovieController;

class Menu extends \App\Core\View\Terminal\Menu {
    protected function getControllers(): array {
        return [
            new MovieController(),
            new DirectorController(),
            new GenreController()
        ];
    }
}