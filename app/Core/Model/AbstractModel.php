<?php

namespace App\Core\Model;
use App\Core\Config;
use App\Core\Database\Connection;
use App\Core\Database\Query\WhereExpression;
use App\Core\Enum\WhereOperator;
use IteratorAggregate;

abstract class AbstractModel {

    /**
     * Data of the given entity
     *
     * @var array
     */
    protected array $data = [];

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
        $this->connection = new Connection(Config::getInstance());
    }

    public function getIdField(): string {
        return 'id';
    }

    /**
     * Return a collection of the current entity
     *
     * @param WhereExpression|null $filter Where clause to filter
     * @param array|null $fields Fields to query. When null will retrieve all of them
     * @return IteratorAggregate
     */
    public function getCollection(WhereExpression $filter = null, array $fields = null): IteratorAggregate {
        return $this->connection->select($this->getTable(), $filter, $fields);
    }

    public function getData(string $field = null): array|string|null {
        if (is_null($field)) {
            return $this->data;
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
        $this->data = $data;
        $this->data[$this->getIdField()] = $this->id;

        return $this;
    }

    public function getId(): int|string|null {
        return $this->id;
    }

    /**
     * @param string|int $id
     * @param string|null $idField
     * @return $this
     */
    public function load(string|int $id, string $idField = null): self {
        $this->data = [];
        $idField = $idField ?? $this->getIdField();
        $result = $this->connection->select($this->getTable(), new WhereExpression($idField, WhereOperator::EQUALS, $id));
        foreach ($result as $row) {
            $this->data = $row;
            break;
        }

        $this->id = $this->data[$idField] ?? null;

        return $this;
    }

}