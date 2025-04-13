<?php

namespace App\Core\Database\Driver;

use App\Core\Database\Column;
use App\Core\Database\DriverInterface;
use App\Core\Database\Query\WhereExpression;
use App\Core\Enum\ColumnType;

class MySQLDriver implements DriverInterface {

    private \mysqli $mysqli;

    function connect(string $host, string $database, string $username, string $password = null): DriverInterface {
        $this->mysqli = mysqli_connect($host, $username, $password, $database);
        return $this;
    }

    function query(string $query): array {
        $result = $this->mysqli->query(mysqli_escape_string($this->mysqli, $query));

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    function select(string $from, WhereExpression $where = null, array $fields = null): array {
        $from = mysqli_escape_string($this->mysqli, $from);
        $fields = mysqli_escape_string($this->mysqli, empty($fields) ? "*" : implode(', ', $fields));
        $query = "SELECT $fields FROM $from";
        if (!is_null($where)) {
            $query .= " WHERE $where";
        }

        $stmt = $this->mysqli->prepare($query);

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    function insert(string $table, array $values): bool {
        $table = mysqli_escape_string($this->mysqli, $table);

        $columns = array_keys($values);
        $escapedColumns = array_map(
            fn($col) => "`" . mysqli_escape_string($this->mysqli, $col) . "`",
            $columns
        );

        $placeholders = array_fill(0, count($values), '?');

        $query = sprintf(
            "INSERT INTO `%s` (%s) VALUES (%s)",
            $table,
            implode(', ', $escapedColumns),
            implode(', ', $placeholders)
        );

        $stmt = $this->mysqli->prepare($query);

        if ($stmt === false) {
            return false;
        }

        $types = '';
        $bindParams = [];

        foreach ($values as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } elseif (is_string($value)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
            $bindParams[] = $value;
        }

        $stmt->bind_param($types, ...$bindParams);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    function update(string $table, array $values, WhereExpression $where = null): bool {
        $table = mysqli_escape_string($this->mysqli, $table);

        $setClauses = [];
        foreach ($values as $column => $value) {
            $escapedColumn = "`" . mysqli_escape_string($this->mysqli, $column) . "`";
            $setClauses[] = "$escapedColumn = ?";
        }

        $query = sprintf(
            "UPDATE `%s` SET %s",
            $table,
            implode(', ', $setClauses)
        );

        if (!is_null($where)) {
            $query .= " WHERE $where";
        }

        $stmt = $this->mysqli->prepare($query);

        if ($stmt === false) {
            return false;
        }

        $types = '';
        $bindParams = [];

        foreach ($values as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } elseif (is_string($value)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
            $bindParams[] = $value;
        }

        $stmt->bind_param($types, ...$bindParams);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    function delete(string $table, WhereExpression $where = null): bool {
        $table = mysqli_escape_string($this->mysqli, $table);

        $query = sprintf("DELETE FROM `%s`", $table);

        if (!is_null($where)) {
            $query .= " WHERE $where";
        }

        $stmt = $this->mysqli->prepare($query);

        if ($stmt === false) {
            return false;
        }

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    function describe(string $table): array {
        $table = mysqli_escape_string($this->mysqli, $table);

        $query = sprintf("SHOW COLUMNS from `%s`", $table);
        $stmt = $this->mysqli->prepare($query);
        if ($stmt === false) {
            return [];
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $columns = [];
        foreach($result->fetch_all(MYSQLI_ASSOC) as $column) {
            $columns[] = new Column(
                name: $column['Field'],
                type: $this->getColumnType($column['Type']),
                isPrimaryKey: $column['Key'] === 'PRI',
                nullable: $column['Null'] === 'NO',
                default: $column['Default']
            );
        }

        return $columns;
    }

    private function getColumnType(string $type): ColumnType {
        if (str_contains($type, 'int')) {
            return ColumnType::INT;
        } else if (str_contains($type, 'float')) {
            return ColumnType::DECIMAL;
        } else if (str_contains($type, 'bool')) {
            return ColumnType::BOOL;
        } else if (str_contains($type, 'date')) {
            return ColumnType::DATE;
        }

        return ColumnType::TEXT;
    }
}