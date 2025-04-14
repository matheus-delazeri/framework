<?php

namespace App\Core\View\Terminal\Model;

use App\Core\Controller\AbstractController;
use App\Core\Model\AbstractModel;
use App\Core\View\Terminal\TerminalView;

class Index extends TerminalView {

    public function _render(AbstractController $controller): void {
        $model = $controller->params['model'] ?? null;
        if (empty($model) || !$model instanceof AbstractModel) {
            throw new \InvalidArgumentException('Controller must have a model as parameter');
        }

        $collection = $model->getCollection();
        printf("\n----- LIST OF %s (%d) -----\n\n", $model->getName(), sizeof($collection));

        /** @var AbstractModel $item */
        foreach ($collection as $item) {
            printf("[%s] %s\n", $item->getIdField(), $item->getId());
        }

        printf("----------------------\n");
        (new Toolbar())->render($controller);
    }

}