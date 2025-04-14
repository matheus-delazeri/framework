<?php

namespace App\Core\View\Terminal\Model;

use App\Core\Controller\AbstractController;
use App\Core\Database\Column;
use App\Core\Model\AbstractModel;
use App\Core\View\Terminal\TerminalView;

class EditForm extends TerminalView {

    /**
     * Model or loaded register to edit or create
     *
     * @var AbstractModel
     */
    protected AbstractModel $model;

    public function __construct(AbstractModel $model) {
        $this->model = $model;
    }

    public function getFields(): array {
        $columns = $this->model->getFields();
        $data = $this->model->getData();
        $fields = array();
        /** @var Column $column */
        foreach ($columns as $column) {
            if ($column->isPrimaryKey) continue;

            $fields[] = [
                'label' => ucfirst(str_replace('_', ' ', $column->name)),
                'field' => $column->name,
                'value' => $data[$column->name] ?? null,
                'type' => $column->getType()
            ];
        }

        return $fields;
    }

    protected function _render(AbstractController $controller): void {
        printf("\n----- [FORM] %s -----\n\n", $this->model->getName());
        if ($this->model->isLoaded()) {
            printf("ID: %s\n", $this->model->getId());
        }

        printf("\n[FIELDS]\n");
        foreach ($this->getFields() as $field) {
            printf("- %s: %s\n", $field['field'], $field['value']);
        }
        printf("\n");
        (new Toolbar())->render($controller);
    }
}