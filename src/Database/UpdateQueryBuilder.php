<?php
declare(strict_types=1);

namespace Database;

use Database\WhereClause\WhereClauseCondition;

class UpdateQueryBuilder
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var MysqlParam[]
     */
    private $fields;

    /**
     * @var WhereClauseCondition[]
     */
    private $whereConditions = [];

    public static function create (): self
    {
        return new static();
    }

    public function setTable (string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @param MysqlParam[] $fields
     * @return UpdateQueryBuilder
     */
    public function setFields (array $fields): self
    {
        $this->fields = $fields;
        return $this;
    }

    public function addWhere (WhereClauseCondition ...$conditions): self
    {
        $this->whereConditions = array_merge($this->whereConditions, $conditions);
        return $this;
    }

    public function build (): Query
    {
        $connection = DatabaseConnection::getInstance();
        $setClauses = [];
        $params = [];

        foreach ($this->fields as $fieldName => $param) {
            $setClauses[] = $connection->quoteIdentifier($fieldName) . ' = ?';
            $params[] = $param;
        }

        $query = 'UPDATE ' . $connection->quoteIdentifier($this->table);
        $query .= ' SET ' . implode(', ', $setClauses);
        $query .= SelectQueryBuilder::buildWhereClause($this->whereConditions, $params);

        return new Query($query, $params);
    }
}