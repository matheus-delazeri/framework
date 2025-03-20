<?php

namespace App\Core\Config\Database;

use App\Core\Config;
use App\Core\Config\Database\Driver\DriverFactory;
use IteratorAggregate;

class Connection {
    private AbstractDriver $driver;

    public function __construct(Config $config) {
        $this->driver = DriverFactory::create($config);
    }

    public function query(string $query): IteratorAggregate
    {
        return $this->driver->rawQuery($query);
    }
}