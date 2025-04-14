<?php

namespace App\Lab\View;

use App\Core\Controller\AbstractController;
use App\Core\View\Terminal\TerminalView;
use App\Lab\Controller\DirectorController;
use App\Lab\Controller\GenreController;
use App\Lab\Controller\MovieController;

class Menu extends TerminalView {

    public static array $MODELS = [
        1 => MovieController::class,
        2 => GenreController::class,
        3 => DirectorController::class
    ];

    protected function _render(AbstractController $controller): void {
        printf("\n----- MENU -----\n\n");

        foreach (self::$MODELS as $idx => $title) {
            printf("[%d] %s\n", $idx, basename(str_replace("\\", "/", $title)));
        }

        printf("\n");
        (new Toolbar())->render($controller);
    }
}