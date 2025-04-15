<?php

namespace App\Core\View\Terminal;

use App\Core\Controller\AbstractController;
use App\Core\Messager;

abstract class Menu extends TerminalView {

    /**
     * Controllers that will be available for access in the
     * menu
     *
     * @return AbstractController[]
     */
    abstract protected function getControllers(): array;

    protected function _render(AbstractController $controller): void {
        printf("\n----- MENU -----\n\n");

        $controllers = $this->getControllers();
        foreach ($controllers as $idx => $class) {
            printf("[%d] %s\n", $idx, basename(str_replace("\\", "/", get_class($class))));
        }

        printf("\n");
        printf("[?] Select a menu option: ");
        $option = $this->getUserInput();
        if (!isset($option, $controllers)) {
            Messager::add("[!] Menu option invalid!");
            $controller->redirectReferer();
        }

        $controller->redirect('index', [], $controllers[$option]);
    }
}