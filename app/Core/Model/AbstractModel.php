<?php

namespace App\Core\Model;
use App\Core\Database\Connection;
use App\Core\Database\Column;
use App\Core\Database\Query\WhereExpression;
use App\Core\Enum\WhereOperator;

abstract class AbstractModel {

    /**
     * Data of the given entity
     *
     * @var array
     */
    private array $data = [];

    /**
     * Associative array where the key is the 
     * field name and the value it's Column object
     * 
     * @var array
     */
    private array $fields = [];

    /**
     * FIXME: doesn't allow tables with compound keys
     * @var string
     */
    private string $idField = 'id';
    protected string|int|null $id = null;

    /**
     * Return the table name of the current entity
     *
     * @return string
     */
    abstract function getTable(): string;

    /**
     * @var Connection DB connection with the driver specified on the config
     */
    private Connection $connection;

    public function __construct() {
        $this->connection = Connection::getInstance();
        $this->prepareFields();
    }

    public function getName(): string {
        return basename(str_replace('\\', '/', get_class($this)));
    }

    private function prepareFields(): void {
        $columns = $this->connection->describe($this->getTable());
        /** @var Column $column */
        foreach ($columns as $column) {
            $this->fields[$column->name] = $column;

            if ($column->isPrimaryKey) {
                $this->idField = $column->name;
            }
        }
    }

    public function getIdField(): string {
        return $this->idField;
    }

    public function getId(): int|string|null {
        return $this->isLoaded() ? $this->getData($this->getIdField()) : null;
    }

    public function getFields(): array {
        return $this->fields;
    }

    /**
     * Return a collection of the current entity
     *
     * @param WhereExpression|null $filter Where clause to filter
     * @param AbstractModel[]|null $fields Fields to query. When null will retrieve all of them
     * @return array
     */
    public function getCollection(WhereExpression $filter = null, array $fields = null): array {
        $items = $this->connection->select($this->getTable(), $filter, $fields);
        $objects = [];
        foreach ($items as $item) {
            $object = new $this;
            $object->setData($item);
            $objects[] = $object;
        }

        return $objects;
    }

    public function getData(string|array $field = null): array|string|null {
        if (!$this->isLoaded()) {
            return array_fill_keys(array_keys($this->getFields()), null);
        }

        if (is_null($field)) {
            return $this->data;
        } else if (is_array($field)) {
            return array_intersect_key($this->data, array_flip($field));
        }

        return $this->data[$field] ?? null;
    }

    /**
     * Set value to specific field
     *
     * @param string $field
     * @param mixed $value
     * @return $this
     */
    public function addData(string $field, mixed $value): self {
        if (!isset($this->fields[$field])) {
            throw new \InvalidArgumentException("Field '$field' does not exists for this model");
        }

        $this->data[$field] = $value;

        return $this;
    }

    /**
     * Overwrite the data of the current entity, keeping its ID
     *
     * @param array $data
     * @return $this
     */
    public function setData(array $data): self {
        $this->data = array_intersect_key($data, $this->fields);
        if (!empty($this->data[$this->getIdField()])) {
            $this->id = $this->data[$this->getIdField()];
        }

        return $this;
    }


    public function isLoaded(): bool {
        return !empty($this->id) && !empty($this->data);
    }

    /**
     * @param string|int $id
     * @param string|null $idField
     * @return $this
     */
    public function load(string|int $id, string $idField = null): self {
        $this->data = [];
        $idField = $idField ?? $this->idField;
        $result = $this->connection->select($this->getTable(), new WhereExpression($idField, WhereOperator::EQUALS, $id));
        foreach ($result as $row) {
            $this->data = $row;
            break;
        }

        $this->id = $this->data[$idField] ?? null;

        return $this;
    }

    /**
     * If the object is loaded will update it with the values of $data.
     * Otherwise, will insert a new register.
     *
     * @return AbstractModel
     */
    public function save(): self {
        if ($this->isLoaded()) {
            $this->connection->update($this->getTable(), $this->data, new WhereExpression($this->idField, WhereOperator::EQUALS, $this->id));
            return $this;
        }

        $this->id = $this->connection->insert($this->getTable(), $this->data);

        return $this;
    }

    public function delete(): self {
        if (!$this->isLoaded()) {
            return $this;
        }

        $this->connection->delete($this->getTable(), new WhereExpression($this->idField, WhereOperator::EQUALS, $this->id));
        return $this;
    }

}