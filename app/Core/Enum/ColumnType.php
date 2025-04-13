<?php

namespace App\Core\Enum;

enum ColumnType: string {
    case INT = 'int';
    case TEXT = 'text';
    case BOOL = 'bool';
    case DECIMAL = 'decimal';
    case DATE = 'date';
}