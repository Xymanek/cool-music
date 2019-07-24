<?php
declare(strict_types=1);

namespace Database;

use Database\WhereClause\WhereClauseCondition;

class SelectQueryBuilder
{
    /**
     * @var string[]
     */
    private $fields = ['*'];

    /**
     * @var string
     */
    private $table;

    /**
     * @var WhereClauseCondition[]
     */
    private $whereConditions = [];

    /**
     * @var OrderClauseEntry[]
     */
    private $orderEntries = [];

    /**
     * @var int|null
     */
    private $limit;

    public static function create (): self
    {
        return new static();
    }

    public function setFields (array $fields): self
    {
        $this->fields = $fields;
        return $this;
    }

    public function setTable (string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function addWhere (WhereClauseCondition $condition): self
    {
        $this->whereConditions[] = $condition;
        return $this;
    }

    /**
     * @param OrderClauseEntry[] $orderEntries
     * @return SelectQueryBuilder
     */
    public function setOrderEntries (OrderClauseEntry ...$orderEntries): self
    {
        $this->orderEntries = $orderEntries;
        return $this;
    }

    /**
     * @param WhereClauseCondition[] $conditions
     * @return SelectQueryBuilder
     */
    public function addMultipleWhere (array $conditions): self
    {
        $this->whereConditions = array_merge($this->whereConditions, $conditions);
        return $this;
    }

    /**
     * @param int|null $limit
     * @return SelectQueryBuilder
     */
    public function setLimit ($limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function build (): Query
    {
        $connection = DatabaseConnection::getInstance();
        $params = [];

        $fields = array_map(function (string $field) use ($connection) : string {
            if ($field === '*') {
                // Do not quote star
                return $field;
            }

            return $connection->quoteIdentifier($field);
        }, $this->fields);

        $query = 'SELECT ' . implode(' ', $fields) . ' ';
        $query .= 'FROM ' . $connection->quoteIdentifier($this->table);
        $query .= self::buildWhereClause($this->whereConditions, $params);
        $query .= self::buildOrderClause($this->orderEntries);

        if ($this->limit !== null) {
            $query .= ' LIMIT ' . $this->limit;
        }

        return new Query($query, $params);
    }

    /**
     * @param WhereClauseCondition[] $conditions
     * @param MysqlParam[]           $params
     * @return string
     */
    public static function buildWhereClause (array $conditions, array &$params): string
    {
        if (count($conditions) === 0) {
            return '';
        }

        $conditionsText = [];
        foreach ($conditions as $condition) {
            $conditionsText[] = $condition->build($params);
        }

        return ' WHERE ' . implode(' AND ', $conditionsText) . ' ';
    }

    /**
     * @param OrderClauseEntry[] $entries
     * @return string
     */
    public static function buildOrderClause (array $entries): string
    {
        if (count($entries) === 0) {
            return '';
        }

        $connection = DatabaseConnection::getInstance();
        $sql = ' ORDER BY ';

        foreach ($entries as $entry) {
            $direction = $entry->isDesc ? ' DESC' : '';
            $column = $entry->column;

            if (!$entry->doNotQuoteColumn) {
                $column = $connection->quoteIdentifier($column);
            }

            $sql .= "{$column}{$direction},";
        }

        // Remove the last comma
        return rtrim($sql, ',');
    }
}