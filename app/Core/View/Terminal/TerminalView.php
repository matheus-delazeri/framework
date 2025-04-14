<?php

namespace App\Core\View\Terminal;

use App\Core\Messager;
use App\Core\View\AbstractView;

abstract class TerminalView extends AbstractView {

    protected function beforeRender(): void {
        $this->clearTerminal();
        $this->renderMessages();

        parent::beforeRender();
    }

    protected function clearTerminal(): void {
        echo "\e[H\e[J";
    }

    protected function renderMessages(): void {
        $messages = Messager::extractAll();
        if (empty($messages)) return;

        printf("\n----- MESSAGES -----\n\n");
        foreach ($messages as $message) {
            printf($message . "\n");
        }

    }

    /**
     * TODO: sanitize user input to avoid injections
     * @return string
     */
    public function getUserInput(): string {
        $fin = fopen ("php://stdin","r");
        return trim(fgets($fin));
    }
}