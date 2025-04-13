<?php

namespace App\Core\View\Terminal;

use App\Core\View\AbstractView;

abstract class TerminalView extends AbstractView {

    protected function beforeRender(): void {
        $this->clearTerminal();
        parent::beforeRender();
    }

    protected function clearTerminal():void {
        echo "\e[H\e[J";
    }

    public function getUserInput(): string {
        $fin = fopen ("php://stdin","r");
        return trim(fgets($fin));
    }
}