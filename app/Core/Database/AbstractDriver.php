<?php

namespace App\Core\Database;

use App\Core\Database\Query\WhereExpression;
use IteratorAggregate;

abstract class AbstractDriver {
    abstract function connect(string $host, string $database, string $username, string $password = null): self;
    abstract function query(string $query): IteratorAggregate;

    /**
     * @param string $from Table name
     * @param WhereExpression|null $where
     * @param array|null $fields Array of fields to retrieve
     * @return IteratorAggregate
     */
    abstract function select(string $from, WhereExpression $where = null, array $fields = null): IteratorAggregate;
}