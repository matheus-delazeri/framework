<?php

namespace App\Core\View\Terminal\Model;

use App\Core\Model\AbstractModel;
use App\Core\View\Terminal\TerminalView;

class IndexView extends TerminalView {

    protected AbstractModel $model;

    public function __construct(AbstractModel $model) {
        $this->model = $model;
    }

    public function _render(\App\Core\Controller\AbstractController $controller): void {
        $collection = $this->model->getCollection();
        printf("\n----- LIST OF %s (%d) -----\n\n", $this->model->getName(), sizeof($collection));

        /** @var AbstractModel $item */
        foreach ($collection as $item) {
            printf(json_encode($item->getData($this->getListableFields())) . "\n");
        }

        printf("----------------------\n");
        (new Toolbar())->render($controller);
    }

    public function getListableFields(): array {
        return [$this->model->getIdField()];
    }
}