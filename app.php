<?php

require_once './vendor/autoload.php';

$env = parse_ini_file('.env');
if (empty($env)) {
    throw new Exception("No .env file found");
}

\App\Core\Config::getInstance($env);

$controller = new \App\Lab\Controller\IndexController();
$controller->redirect('index');