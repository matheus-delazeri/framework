<?php

namespace App\Core\Database;

use App\Core\Enum\ColumnType;

class Column {
    public string $name;
    public ColumnType $type;
    public bool $isPrimaryKey = false;
    public bool $nullable = false;
    public mixed $default;

    public function __construct(
        string $name,
        ColumnType $type = ColumnType::TEXT,
        bool $isPrimaryKey = false,
        bool $nullable = false,
        mixed $default = null) {

        $this->name = $name;
        $this->type = $type;
        $this->isPrimaryKey = $isPrimaryKey;
        $this->nullable = $nullable;
        $this->default = $default;
    }

    public function getType(): string {
        return $this->type->value;
    }

}