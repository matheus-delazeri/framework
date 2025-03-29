<?php

namespace App\Core;

/** Singleton class */
class Config extends Singleton {

    public static array $config = [];

    public static function getInstance(array $config = []): Config {
        /** @var Config $instance */
        $instance = parent::getInstance();
        if (!empty($config))  $instance::$config = $config;

        return $instance;
    }

    public static function getValue(string $field): mixed {
        return self::$config[$field] ?? null;
    }
}