<?php

namespace App\Core\Config\Database\Driver;

use App\Core\Config;
use App\Core\Config\Database\AbstractDriver;

class DriverFactory {
    public static function create(Config $config): ?AbstractDriver {
        $driver = null;
        switch ($config::getValue('DB_DRIVER')) {
            case 'mysql':
                $driver = new MySQLDriver();
        }

        if ($driver instanceof AbstractDriver) {
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