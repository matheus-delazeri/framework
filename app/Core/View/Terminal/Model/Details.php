<?php

namespace App\Core\View\Terminal\Model;

use App\Core\Controller\AbstractController;
use App\Core\Model\AbstractModel;
use App\Core\View\Terminal\TerminalView;

class Details extends TerminalView {

    public function _render(AbstractController $controller): void {
        $model = $controller->params['model'] ?? null;
        if (empty($model) || !$model instanceof AbstractModel) {
            throw new \InvalidArgumentException('Controller must have a model as parameter');
        }

        printf("\n----- [Model] %s -----\n\n- Class: %s\n- Table: %s\n- Registers: %d\n\n",
            $model->getName(),
            get_class($model),
            $model->getTable(),
            count($model->getCollection())
        );

        $fields = $model->getFields();
        printf("[FIELDS]\n");
        foreach ($fields as $field => $column) {
            printf("- %s (%s)\n", $field, $column->getType());
        }
        printf("\n");
        (new Toolbar())->render($controller);
    }

}