<?php

namespace App\Core\Config\Database\Driver;

use App\Core\Config\Database\AbstractDriver;
use IteratorAggregate;

class MySQLDriver extends AbstractDriver {

    private \mysqli $mysqli;

    function connect(string $host, string $database, string $username, string $password = null): AbstractDriver {
        $this->mysqli = mysqli_connect($host, $username, $password, $database);

        return $this;
    }

    function rawQuery(string $query): IteratorAggregate
    {
        return $this->mysqli->query($query);
    }

    function select(string $from, array $fields = [], string $where = null): IteratorAggregate
    {
        // TODO: Implement select() method.
    }
}