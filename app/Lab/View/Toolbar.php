<?php

namespace App\Lab\View;

use App\Core\Controller\AbstractController;
use App\Core\Messager;

class Toolbar extends \App\Core\View\Terminal\Model\Toolbar {

    protected array $operations = [
        'q' => [
            'name' => 'Quit',
            'description' => 'Quit application'
        ],
        'h' => [
            'name' => 'Help',
            'description' => 'Shows help about each operation'
        ],
        'm' => [
            'name' => 'Model',
            'description' => 'Select a model to view'
        ]
    ];

    protected function beforeRender(): void {
        return;
    }

    public function _render(AbstractController $controller): void {
        $availableOperations = ['q', 'h'];

        switch ($controller->currentAction) {
            case 'index':
                $availableOperations = array_merge($availableOperations, ['m']);
                break;
        }

        $message = "[?] Select an operation: ";
        foreach ($availableOperations as $operation) {
            $message .= "($operation)".$this->operations[$operation]['name']." ";
        }

        printf("%s\n> ", $message);
        switch (strtolower($this->getUserInput())) {
            case 'q':
                exit(0);
            case 'h':
                $this->showHelp($availableOperations);
                $this->_render($controller);
                break;
            case 'm':
                printf("[?] Type the index of the model to view:\n> ");
                $modelIdx = $this->getUserInput();
                if (!isset(Menu::$MODELS[$modelIdx])) {
                    Messager::add("[!] Invalid model selected!");
                    $controller->redirectReferer();
                } else {
                    $controller->redirect('index', [], new Menu::$MODELS[$modelIdx]());
                }
                break;
            default:
                Messager::add("[!] Operation not found!");
                $controller->redirectReferer();
        }
    }
}