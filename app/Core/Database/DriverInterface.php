<?php

namespace App\Core\Database;

use App\Core\Database\Query\WhereExpression;

interface DriverInterface {
    function connect(string $host, string $database, string $username, string $password = null): self;

    /**
     * Allow the execution of a raw query
     *
     * @param string $query
     * @return array
     */
    function query(string $query): array;

    /**
     * @param string $from Table name
     * @param WhereExpression|null $where
     * @param array|null $fields Array of fields to retrieve
     * @return array
     */
     function select(string $from, WhereExpression $where = null, array $fields = null): array;

    /**
     * @param string $table
     * @param array $values
     * @return bool
     */
     function insert(string $table, array $values): bool;

    /**
     * @param string $table
     * @param array $values
     * @param WhereExpression|null $where
     * @return bool
     */
     function update(string $table, array $values, WhereExpression $where = null): bool;

    /**
     * @param string $table
     * @param WhereExpression|null $where
     * @return bool
     */
     function delete(string $table, WhereExpression $where = null): bool;

    /**
     * Return all columns from a given table
     *
     * @param string $table
     * @return Column[]
     */
     function describe(string $table): array;
}