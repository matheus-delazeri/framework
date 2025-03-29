<?php

namespace App\Core\Database;

use App\Core\Config;
use App\Core\Database\Driver\DriverFactory;
use App\Core\Database\Query\WhereExpression;
use IteratorAggregate;

class Connection {

    private AbstractDriver $driver;

    public function __construct(Config $config) {
        $this->driver = DriverFactory::create($config);
    }

    public function query(string $query): IteratorAggregate {
        return $this->driver->query($query);
    }

    public function select(string $from, WhereExpression $where = null, array $fields = null): IteratorAggregate {
        return $this->driver->select($from, $where, $fields);
    }

}