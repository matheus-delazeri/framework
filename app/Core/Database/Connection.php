<?php

namespace App\Core\Database;

use App\Core\Config;
use App\Core\Database\Driver\DriverFactory;
use App\Core\Database\Query\WhereExpression;
use IteratorAggregate;

class Connection {

    private DriverInterface $driver;

    public function __construct(Config $config) {
        $this->driver = DriverFactory::create($config);
    }

    public function query(string $query): array {
        return $this->driver->query($query);
    }

    public function select(string $from, WhereExpression $where = null, array $fields = null): array {
        return $this->driver->select($from, $where, $fields);
    }

    public function insert(string $table, array $values): bool {
        return $this->driver->insert($table, $values);
    }

    public function update(string $table, array $values, WhereExpression $where = null): bool {
        return $this->driver->update($table, $values, $where);
    }

    public function delete(string $table, WhereExpression $where = null): bool {
        return $this->driver->delete($table, $where);
    }
}