<?php

namespace App\Core\Model;
use App\Core\Config;
use App\Core\Config\Database\Connection;

class Core {

    private Connection $connection;

    public function __construct() {
        $this->connection = new Connection(Config::getInstance());
    }
}