<?php
declare(strict_types=1);

namespace Database\WhereClause;

use Database\DatabaseConnection;
use Database\MysqlParam;

class ComparisonCondition implements WhereClauseCondition
{
    /**
     * @var string
     */
    public $column;

    /**
     * @var string
     */
    public $comparison;

    /**
     * @var MysqlParam
     */
    public $value;

    public function __construct (string $column, string $comparison, MysqlParam $value)
    {
        $this->column = $column;
        $this->comparison = $comparison;
        $this->value = $value;
    }

    public static function equals (string $column, MysqlParam $value)
    {
        return new static($column, '=', $value);
    }

    public static function notEquals (string $column, MysqlParam $value)
    {
        return new static($column, '!=', $value);
    }

    public static function like (string $column, MysqlParam $value)
    {
        return new static($column, 'LIKE', $value);
    }

    public function build (array &$params): string
    {
        $params[] = $this->value;

        $connection = DatabaseConnection::getInstance();
        $column = $connection->quoteIdentifier($this->column);

        return "{$column} {$this->comparison} ?";
    }
}