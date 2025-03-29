<?php

namespace App\Core\Database\Driver;

use App\Core\Config;
use App\Core\Database\DriverInterface;

class DriverFactory {
    public static function create(Config $config): ?DriverInterface{
        $driver = null;
        switch ($config::getValue('DB_DRIVER')) {
            case 'mysql':
                $driver = new MySQLDriver();
        }

        if ($driver instanceof DriverInterface) {
            $driver->connect(
                $config::getValue('DB_HOST'),
                $config::getValue('DB_DATABASE'),
                $config::getValue('DB_USERNAME'),
                $config::getValue('DB_PASSWORD')
            );
        }

        return $driver;
    }
}