<?php

namespace App\Core;

/** Singleton class */
class Config {

    private static Config $instance;
    private static array $config = [];

    public function __construct(array $config = []) {
        self::$config = $config;
        self::$instance = $this;
    }

    public static function getInstance(array $config = []): Config {
        if (!isset(self::$instance)) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    public static function getValue(string $field) {
        return self::$config[$field] ?? null;
    }
}