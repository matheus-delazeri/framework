<?php

use App\Core\Database\Query\WhereExpression;

require_once './vendor/autoload.php';

$env = parse_ini_file('.env');
if (empty($env)) {
    throw new Exception("No .env file found");
}

$config = \App\Core\Config::getInstance($env);
$movie = new \App\Core\Model\Movie();