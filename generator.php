<?php
/**
 * App Generator CLI Script
 *
 * Usage:
 * php generate-app.php --env=.env --app=AdminPanel --tables=users,roles
 * php generate-app.php --env=.env --app=AdminPanel --all
 */
require_once './vendor/autoload.php';

use App\Core\Config;
use App\Core\Generator;

$params = getopt('', ['env::', 'app::', 'tables::', 'all', 'template::']);
$envFile = $params['env'] ?? '.env';
$appName = $params['app'] ?? null;
$tables = isset($params['tables']) ? explode(',', $params['tables']) : [];
$allTables = isset($params['all']);
$templateName = $params['template'] ?? 'TerminalApp';

if (empty($appName)) {
    echo "[!] App name is required. Use --app=YourAppName\n";
    exit(1);
}

try {
    $env = @parse_ini_file($envFile);
    if (empty($env)) {
        throw new Exception("No '$envFile' file found");
    }
} catch (Exception $e) {
    echo "[!] Invalid environment file '$envFile': {$e->getMessage()}\n";
    exit(1);
}

Config::getInstance($env);

try {
    $generator = new Generator();

    if ($allTables) {
        echo "[*] Generating app '$appName' with all tables...\n";
        $tables = $generator->getTables();
    }

    if (!empty($tables)) {
        echo "[*] Processing tables: " . implode(', ', $tables) . "\n";

        $generatedEntities = $generator->generateApp($appName, $tables, $templateName);

        if (!empty($generatedEntities)) {
            echo "[✓] Successfully generated app '$appName' with " . count($generatedEntities) . " entities\n";
            echo "    Generated entities: " . implode(', ', $generatedEntities) . "\n";
        } else {
            echo "[!] No entities were generated. Check for errors above.\n";
            exit(1);
        }
    } else {
        echo "[!] No tables specified. Use --tables=table1,table2 or --all\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "[!] Error: {$e->getMessage()}\n";
    exit(1);
}

echo "[✓] App generation complete!\n";