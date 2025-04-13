<?php

namespace App\Core\View\Terminal\Model;

use App\Core\Controller\AbstractController;
use App\Core\Database\Column;
use App\Core\Model\AbstractModel;
use App\Core\View\Terminal\TerminalView;

class EditForm extends TerminalView {

    protected string $title;
    protected string $subtitle;
    protected AbstractModel $model;

    public function __construct(AbstractModel $model, $title = null) {
        $this->model = $model;
        $this->title = $title ??  'Form ' . $model::class;
        $this->subtitle = 'ID: ' . $model->getId();
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getSubtitle(): string {
        return $this->subtitle;
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

    protected function _render(AbstractController $controller): void
    {
        // TODO: Implement _render() method.
    }
}