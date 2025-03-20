<?php

require_once './vendor/autoload.php';

$env = parse_ini_file('.env');
if (empty($env)) {
    throw new Exception("No .env file found");
}

$config = new \App\Core\Config($env);
$core = new \App\Core\Model\Core();