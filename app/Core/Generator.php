<?php

namespace App\Core;

use App\Core\Database\Column;
use App\Core\Database\Connection;

class Generator
{
    private string $templateDir;
    private string $outputDir;
    private Connection $connection;

    const PLACEHOLDER_APP = '__APP__';
    const PLACEHOLDER_ENTITY = '__ENTITY__';
    const PLACEHOLDER_TABLE = '__TABLE__';
    const PLACEHOLDER_CONTROLLERS_ARRAY = '__CONTROLLERS_ARRAY__';

    /**
     * Constructor
     *
     * @param string $templateDir Directory containing templates
     * @param string $outputDir Directory to output generated files
     */
    public function __construct(string $templateDir = './lib/templates', string $outputDir = './app')
    {
        $this->templateDir = rtrim($templateDir, '/');
        $this->outputDir = rtrim($outputDir, '/');
        $this->connection = Connection::getInstance();
    }

    /**
     * Get all tables from the database
     *
     * @return array List of table names
     */
    public function getTables(): array
    {
        $dbName = Config::getValue('DB_NAME');
        if (empty($dbName)) {
            throw new \Exception("Database name not configured in config");
        }

        return $this->connection->tables();
    }

    /**
     * Generate a new app with all entities
     *
     * @param string $appName Name of the app
     * @param array $tables List of tables to include
     * @param string $templateName Template to use (default: TerminalApp)
     * @return array List of generated entities
     */
    public function generateApp(string $appName, array $tables = [], string $templateName = 'TerminalApp'): array
    {
        if (!$this->validateAppName($appName)) {
            throw new \Exception("Invalid app name: $appName");
        }

        $templatePath = "{$this->templateDir}/$templateName";
        if (!is_dir($templatePath)) {
            throw new \Exception("Template directory not found: $templatePath");
        }

        if (empty($tables)) {
            $tables = $this->getTables();
        }

        $appDir = $this->createAppBaseStructure($appName);
        if (!$appDir) {
            throw new \Exception("An app with the name '$appName' already exists.");
        }

        $modelTemplate = $this->loadTemplateFile($templatePath, self::PLACEHOLDER_ENTITY . '.php', 'Model');
        $controllerTemplate = $this->loadTemplateFile($templatePath, self::PLACEHOLDER_ENTITY . 'Controller.php', 'Controller');
        $indexControllerTemplate = $this->loadTemplateFile($templatePath, 'IndexController.php', 'Controller');
        $menuTemplate = $this->loadTemplateFile($templatePath, 'Menu.php', 'View');

        $generatedEntities = [];
        foreach ($tables as $tableName) {
            try {
                $columns = $this->connection->describe($tableName);
                if (empty($columns)) {
                    throw new \Exception("Table '$tableName' not found or has no columns");
                }

                $entityName = $this->getEntityNameFromTable($tableName);
                $this->generateModelFile($appName, $entityName, $tableName, $appDir, $modelTemplate);
                $this->generateExampleEntityFile($appName, $entityName, $columns);
                $this->generateControllerFile($appName, $entityName, $appDir, $controllerTemplate);

                $generatedEntities[] = $entityName;
            } catch (\Exception $e) {
                echo "[!] Error processing table '$tableName': {$e->getMessage()}\n";
            }
        }

        if (!empty($generatedEntities)) {
            $this->generateMenuFile($appName, $generatedEntities, $appDir, $menuTemplate);
            $this->generateControllerFile($appName, 'Index', $appDir, $indexControllerTemplate);
            $this->generateExampleAppFile($appName);
        }

        return $generatedEntities;
    }

    /**
     * Load a template file
     *
     * @param string $templatePath Path to template directory
     * @param string $filename Template filename
     * @param string $subdir Subdirectory (Model/Controller/View)
     * @return string Template content
     */
    private function loadTemplateFile(string $templatePath, string $filename, string $subdir): string
    {
        $filePath = "$templatePath/" . self::PLACEHOLDER_APP . "/$subdir/$filename";

        if (!file_exists($filePath)) {
            throw new \Exception("Template file not found: $filePath");
        }

        return file_get_contents($filePath);
    }

    /**
     * Create base application directory structure
     *
     * @param string $appName Name of the app
     * @return string Path to the app directory
     */
    private function createAppBaseStructure(string $appName): string {
        $appDir = "{$this->outputDir}/$appName";
        if (is_dir($appDir)) { return false;}

        mkdir($appDir, 0755, true);

        $directories = ['Controller', 'Model', 'View'];
        foreach ($directories as $dir) {
            if (!is_dir("$appDir/$dir")) {
                mkdir("$appDir/$dir", 0755, true);
            }
        }

        return $appDir;
    }

    /**
     * Generate model file
     *
     * @param string $appName Name of the app
     * @param string $entityName Name of the entity
     * @param string $tableName Name of the database table
     * @param string $appDir App directory path
     * @param string $template Template content
     */
    private function generateModelFile(
        string $appName,
        string $entityName,
        string $tableName,
        string $appDir,
        string $template
    ): void {
        $content = str_replace(
            [self::PLACEHOLDER_APP, self::PLACEHOLDER_ENTITY, self::PLACEHOLDER_TABLE],
            [$appName, $entityName, "'$tableName'"],
            $template
        );

        file_put_contents("$appDir/Model/$entityName.php", $content);
    }

    private function generateExampleEntityFile(
        string $appName,
        string $entityName,
        array $columns
    ): void {
        $exampleDir = "./examples/$appName/Entities";
        if (!is_dir($exampleDir)) {
            mkdir($exampleDir, 0755, true);
        }

        $className = "\\App\\$appName\\Model\\$entityName";

        $fieldAssignmentsInsert = '';
        $fieldAssignmentsUpdate = '';
        $firstUpdatableField = null;

        /** @var Column $column */
        foreach ($columns as $column) {
            $exampleValue = $this->getExampleValueForType($column->type->name, $column->name);
            $fieldAssignmentsInsert .= "    \$model->addData('{$column->name}', {$exampleValue});\n";

            if (!$firstUpdatableField && !$column->isPrimaryKey) {
                $firstUpdatableField = $column->name;
                $newExampleValue = $this->getExampleValueForType($column->type->name, $column->name, true);
                $fieldAssignmentsUpdate = "    \$model->addData('{$column->name}', {$newExampleValue});\n";
            }
        }

        $exampleContent = <<<PHP
    <?php

    require_once __DIR__ . '/../../../vendor/autoload.php';
    
    \$env = parse_ini_file('.env');
    if (empty(\$env)) {
        throw new Exception("No .env file found");
    }

    \App\Core\Config::getInstance(\$env);

    echo "== Example: $entityName CRUD ==\\n";

    \$model = new $className();

    // Create
    $fieldAssignmentsInsert
    \$model->save();
    echo "Created with ID: " . \$model->getId() . "\\n";
    \$entityId = \$model->getId();

    // Read
    \$model = new $className();
    \$model->load(\$entityId);
    print_r(\$model->getData());

    // Update
    \$model = new $className();
    \$model->load(\$entityId);
    $fieldAssignmentsUpdate
    \$model->save();
    echo "Updated.\\n";

    // Delete
    \$model->delete();
    echo "Deleted.\\n";

    PHP;

        file_put_contents("$exampleDir/{$entityName}Example.php", $exampleContent);
    }

    private function generateExampleAppFile(
        string $appName
    ): void {
        $exampleDir = "./examples/$appName";
        if (!is_dir($exampleDir)) {
            mkdir($exampleDir, 0755, true);
        }

        $exampleContent = <<<PHP
        <?php

        require_once __DIR__ . '/../../vendor/autoload.php';

        \$env = parse_ini_file('.env');
        if (empty(\$env)) {
        throw new Exception("No .env file found");
        }

        \App\Core\Config::getInstance(\$env);
        \$controller = new \App\\$appName\Controller\IndexController();
        \$controller->redirect('index');

        PHP;

        file_put_contents("$exampleDir/app.php", $exampleContent);
    }

    private function getExampleValueForType(string $type, string $fieldName = '', bool $forUpdate = false): string {
        $type = strtolower($type);
        $suffix = $forUpdate ? '_updated' : '';

        if (str_contains($type, 'int')) {
            return $forUpdate ? rand(1000, 9999) : rand(1, 100);
        }

        if (str_contains($type, 'char') || str_contains($type, 'text')) {
            return "'" . ucfirst($fieldName) . $suffix . "'";
        }

        if (str_contains($type, 'date')) {
            return "'" . ($forUpdate ? '2025-01-01' : '2024-01-01') . "'";
        }

        if (str_contains($type, 'bool') || $type === 'tinyint(1)') {
            return $forUpdate ? 'false' : 'true';
        }

        if (str_contains($type, 'float') || str_contains($type, 'double') || str_contains($type, 'decimal')) {
            return $forUpdate ? '456.78' : '123.45';
        }

        return "'Sample{$suffix}'";
    }

    /**
     * Generate controller file
     *
     * @param string $appName Name of the app
     * @param string $controllerName Name of the entity
     * @param string $appDir App directory path
     * @param string $template Template content
     */
    private function generateControllerFile(
        string $appName,
        string $controllerName,
        string $appDir,
        string $template
    ): void {
        $content = str_replace(
            [self::PLACEHOLDER_APP, self::PLACEHOLDER_ENTITY],
            [$appName, $controllerName],
            $template
        );

        file_put_contents("$appDir/Controller/{$controllerName}Controller.php", $content);
    }

    /**
     * Generate menu view file
     *
     * @param string $appName Name of the app
     * @param array $entities List of entity names
     * @param string $appDir App directory path
     * @param string $template Template content
     */
    private function generateMenuFile(string $appName, array $entities, string $appDir, string $template): void {
        $controllersArray = "[\n";
        foreach ($entities as $entity) {
            $controllersArray .= "        new \\App\\$appName\\Controller\\{$entity}Controller(),\n";
        }
        $controllersArray .= "    ]";

        $content = str_replace(
            [self::PLACEHOLDER_APP, self::PLACEHOLDER_CONTROLLERS_ARRAY],
            [$appName, $controllersArray],
            $template
        );

        file_put_contents("$appDir/View/Menu.php", $content);
    }

    /**
     * Validate app name
     *
     * @param string $appName Name to validate
     * @return bool
     */
    private function validateAppName(string $appName): bool {
        return preg_match('/^[A-Z][a-zA-Z0-9]*$/', $appName) === 1;
    }

    /**
     * Convert table name to entity name (singular)
     *
     * @param string $tableName Table name
     * @return string Entity name
     */
    private function getEntityNameFromTable(string $tableName): string {
        $name = preg_replace('/^(tbl_|tb_|t_)/', '', $tableName);

        if (substr($name, -1) === 's') {
            $name = substr($name, 0, -1);
        } elseif (substr($name, -3) === 'ies') {
            $name = substr($name, 0, -3) . 'y';
        } elseif (substr($name, -3) === 'ses') {
            $name = substr($name, 0, -2);
        }

        $parts = explode('_', $name);
        $entityName = '';
        foreach ($parts as $part) {
            $entityName .= ucfirst(strtolower($part));
        }

        return $entityName;
    }
}