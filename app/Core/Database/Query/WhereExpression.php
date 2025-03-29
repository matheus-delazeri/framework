<?php

namespace App\Core\Database\Query;

use App\Core\Enum\WhereOperator;

class WhereExpression {

    /**
     * Determines the logical clauses (OR or AND) for each
     * condition
     *
     * @var array
     */
    protected array $logical = [];
    protected array $conditions = [];

    /**
     * Constructs the where expression with an initial condition
     *
     * @param string $field
     * @param WhereOperator $operator
     * @param string $value
     */
    public function __construct(string $field, WhereOperator $operator, string $value) {
        $this->addCondition($field, $operator, $value);
    }

    /**
     * @param string $field
     * @param WhereOperator $operator
     * @param string $value
     */
    protected function addCondition(string $field, WhereOperator $operator, string $value): void {
        $this->conditions[] = [$field, $operator, $value];
    }

    public function and(string $field, WhereOperator $operator, string $value): self {
        $this->logical[] = 'AND';
        $this->addCondition($field, $operator, $value);

        return $this;
    }

     public function or(string $field, WhereOperator $operator, string $value): self {
        $this->logical[] = 'OR';
        $this->addCondition($field, $operator, $value);

        return $this;
    }

    public function __toString(): string {
        $expression = '';
        for ($i = 0; $i < count($this->conditions); $i++) {
            [$field, $operator, $value] = $this->conditions[$i];
            $expression .= "{$field} {$operator->value} {$this->formatValue($value)}";
            if ($i !== count($this->conditions) - 1) {
                $expression .= $this->logical[$i];
            }
        }

        return $expression;
    }

    /**
     * Formats a value for use in SQL based on its type
     *
     * @param mixed $value
     * @return string
     */
    private function formatValue(mixed $value): string {
        if (is_null($value)) {
            return 'NULL';
        } elseif (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        } elseif (is_numeric($value)) {
            return (string)$value;
        } elseif (is_array($value)) {
            $formattedValues = array_map(fn($v) => $this->formatValue($v), $value);
            return '(' . implode(', ', $formattedValues) . ')';
        } else {
            return "'" . addslashes($value) . "'";
        }
    }
}