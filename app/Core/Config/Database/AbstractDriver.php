<?php

namespace App\Core\Config\Database;

use IteratorAggregate;

abstract class AbstractDriver {
    abstract function connect(string $host, string $database, string $username, string $password = null): self;
    abstract function rawQuery(string $query): IteratorAggregate;

    /**
     * @param string $from Table name
     * @param array $fields Array of fields to retrieve
     * @param string|null $where Where condition, using the MySQL pattern.
     * @return IteratorAggregate
     */
    abstract function select(string $from, array $fields = [], string $where = null): IteratorAggregate;
}