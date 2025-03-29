<?php

namespace App\Core\Database\Driver;

use App\Core\Database\AbstractDriver;
use App\Core\Database\Query\WhereExpression;
use IteratorAggregate;

class MySQLDriver extends AbstractDriver {

    private \mysqli $mysqli;

    function connect(string $host, string $database, string $username, string $password = null): AbstractDriver {
        $this->mysqli = mysqli_connect($host, $username, $password, $database);
        return $this;
    }

    function query(string $query): IteratorAggregate {
        return $this->mysqli->query(mysqli_escape_string($this->mysqli, $query));
    }

    function select(string $from, WhereExpression $where = null, array $fields = null): IteratorAggregate {
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

        return $result;
    }
}