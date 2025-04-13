<?php

namespace App\Core\View\Terminal\Model;

use App\Core\Controller\AbstractController;
use App\Core\Controller\Terminal\ModelController;
use App\Core\Controller\TerminalController;

class Toolbar extends \App\Core\View\Terminal\TerminalView {

    private static array $operations = [
        'h' => [
            'name' => 'Help',
            'description' => 'Shows help about each operation'
        ],
        'b' => [
            'name' => 'Back',
            'description' => 'Go back to previous controller'
        ],
        'm' => [
            'name' => 'Model',
            'description' => 'Shows details about the current model'
        ],
        'i' => [
            'name' => 'Index',
            'description' => 'Go to index view of the model'
        ],
        'e' => [
            'name' => 'Edit',
            'description' => 'Select a given register from the list to edit'
        ],
        's' => [
            'name' => 'Save',
            'description' => 'Save current register being edited'
        ],
        'd' => [
            'name' => 'Delete',
            'description' => 'Delete current register'
        ]
    ];

    protected function beforeRender(): void {
    }

    public function _render(AbstractController $controller): void {
        $availableOperations = [];
        switch ($controller->currentAction) {
            case 'index':
                $availableOperations = ['h', 'b', 'm', 'e'];
                break;
        }

        $message = "Select an operation: ";
        foreach ($availableOperations as $operation) {
            $message .= "($operation)".self::$operations[$operation]['name']." ";
        }

        printf("%s\n> ", $message);
        switch ($this->getUserInput()) {
            case 'h':
                $this->showHelp($availableOperations);
                $this->_render($controller);
                break;
            default:
                printf("[!] Operation not found!\n");
                $this->_render($controller);
        }
    }

    private function showHelp(array $operations): void {
        printf("----- HELP -----\n\n");
        foreach ($operations as $operation) {
            printf("(%s)%s ---------- %s\n", $operation, self::$operations[$operation]['name'], self::$operations[$operation]['description']);
        }
        printf("\n");
    }
}