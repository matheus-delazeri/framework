<?php

namespace App\Core\Enum;

enum WhereOperator: string {
    case EQUALS = "=";
    case NOT_EQUALS = "!=";
    case GREATER_THAN = ">";
    case LESS_THAN = "<";
    case GREATER_THAN_OR_EQUAL = ">=";
    case LESS_THAN_OR_EQUAL = "<=";
    case LIKE = "LIKE";
    case NOT_LIKE = "NOT LIKE";
    case IN = "IN";
    case NOT_IN = "NOT IN";
    case BETWEEN = "BETWEEN";
    case NOT_BETWEEN = "NOT BETWEEN";
    case IS_NULL = "IS NULL";
    case IS_NOT_NULL = "IS NOT NULL";
    case AND = "AND";
    case OR = "OR";
}