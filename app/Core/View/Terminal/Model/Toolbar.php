<?php

namespace App\Core\View\Terminal\Model;

use App\Core\Controller\AbstractController;
use App\Core\Messager;
use App\Core\Model\AbstractModel;
use App\Core\View\Terminal\TerminalView;

class Toolbar extends TerminalView {

    protected array $operations = [
        'q' => [
            'name' => 'Quit',
            'description' => 'Quit application'
        ],
        'h' => [
            'name' => 'Help',
            'description' => 'Shows help about each operation'
        ],
        'b' => [
            'name' => 'Back',
            'description' => 'Go back to previous controller'
        ],
        'd' => [
            'name' => 'Details',
            'description' => 'Shows details about the current model'
        ],
        'i' => [
            'name' => 'Index',
            'description' => 'Go to index view of the model'
        ],
        'v' => [
            'name' => 'View',
            'description' => 'Open a selected register for visualization'
        ],
        'e' => [
            'name' => 'Edit',
            'description' => 'Select and update a field of the current register'
        ],
        'r' => [
            'name' => 'Remove',
            'description' => 'Delete the current register'
        ]
    ];

    protected function beforeRender(): void {
        return;
    }

    public function _render(AbstractController $controller): void {
        $availableOperations = ['q', 'h'];

        $model = $controller->params['model'] ?? null;
        if (empty($model) || !$model instanceof AbstractModel) {
            throw new \InvalidArgumentException('Controller must have a model as parameter');
        }

        switch ($controller->currentAction) {
            case 'index':
                $availableOperations = array_merge($availableOperations, ['b', 'd', 'v']);
                break;
            case 'details':
                $availableOperations = array_merge($availableOperations, ['b', 'i']);
                break;
            case 'view':
                $availableOperations = array_merge($availableOperations, ['b', 'i', 'd', 'e', 'r']);
                break;
        }

        $message = "[?] Select an operation: ";
        foreach ($availableOperations as $operation) {
            $message .= "($operation)".$this->operations[$operation]['name']." ";
        }

        printf("%s\n> ", $message);
        $input = strtolower($this->getUserInput());
        if (!in_array($input, $availableOperations)) {
            Messager::add("[!] Operation not found!");
            $controller->redirectReferer();
            return;
        }

        switch ($input) {
            case 'q':
                exit(0);
            case 'h':
                $this->showHelp($availableOperations);
                $this->_render($controller);
                break;
            case 'b':
                $controller->redirectReferer();
                break;
            case 'i':
                $controller->redirect('index');
                break;
            case 'd':
                $controller->redirect('details');
                break;
            case 'v':
                printf("[?] Type the '%s' of the %s you want to view:\n> ", $model->getIdField(), $model->getName());
                $registerId = $this->getUserInput();
                $controller->redirect('view', ['id' => $registerId]);
                break;
            case 'e':
                printf("[?] Type the field you want to edit:\n> ");
                $field = $this->getUserInput();
                printf("[?] Type the new value for the field '%s':\n> ", $field);
                $value = $this->getUserInput();
                $params = $controller->params;
                $params['field'] = $field;
                $params['value'] = $value;

                $controller->redirect('update', $params);
                break;
            case 'r':
                $params = $controller->params;
                printf("[?] Are you sure you want to delete register '%s'? (y/n)\n> ", $params['id']);
                $response = $this->getUserInput();
                if (strtolower($response) != 'y') {
                    $this->_render($controller);
                } else {
                    $controller->redirect('delete', $params);
                }
                break;
            default:
                Messager::add("[!] Operation not found!");
                $controller->redirectReferer();
        }
    }

    protected function showHelp(array $operations): void {
        printf("----- HELP -----\n\n");
        foreach ($operations as $operation) {
            printf("(%s)%s ---------- %s\n", $operation, $this->operations[$operation]['name'], $this->operations[$operation]['description']);
        }
        printf("\n");
    }
}