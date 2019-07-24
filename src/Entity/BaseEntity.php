<?php
declare(strict_types=1);

namespace Entity;

use Database\DatabaseConnection;
use Database\Exception\MultipleResultsException;
use Database\Exception\MultipleRowsAffectException;
use Database\Exception\NoResultsException;
use Database\Exception\NoRowsAffectedException;
use Database\MysqlParam;
use Database\Query;
use Database\SelectQueryBuilder;
use Database\UpdateQueryBuilder;
use Database\WhereClause\ComparisonCondition;
use Database\WhereClause\WhereClauseCondition;
use LogicException;
use mysqli_stmt;

abstract class BaseEntity
{
    /**
     * @return static[]
     */
    public static function fetchAll (): array
    {
        $query = SelectQueryBuilder::create()->setTable(static::getTableName())->build();
        $records = DatabaseConnection::getInstance()->fetchResults($query);

        return array_map(static::class . '::mapFromDatabase', $records);
    }

    /**
     * @param WhereClauseCondition $firstCondition
     * @param WhereClauseCondition ...$moreConditions
     *
     * @return static
     *
     * @throws MultipleResultsException
     * @throws NoResultsException
     */
    public static function fetchOneByCriteria (
        WhereClauseCondition $firstCondition, // Separated to make it required
        WhereClauseCondition ...$moreConditions
    ) {
        array_unshift($moreConditions, $firstCondition);

        $query = SelectQueryBuilder::create()
            ->setTable(static::getTableName())
            ->addMultipleWhere($moreConditions)
            ->build();

        return static::mapFromDatabase(DatabaseConnection::getInstance()->fetchSingleResult($query));
    }

    /**
     * @param WhereClauseCondition ...$conditions
     *
     * @return static[]
     */
    public static function fetchByCriteria (WhereClauseCondition ...$conditions)
    {
        $query = SelectQueryBuilder::create()
            ->setTable(static::getTableName())
            ->addMultipleWhere($conditions)
            ->build();

        $records = DatabaseConnection::getInstance()->fetchResults($query);

        return array_map(static::class . '::mapFromDatabase', $records);
    }

    /**
     * @param SelectQueryBuilder $builder
     * @return static[]
     */
    public static function fetchByBuilder (SelectQueryBuilder $builder)
    {
        $query = $builder->setTable(static::getTableName())->build();
        $records = DatabaseConnection::getInstance()->fetchResults($query);

        return array_map(static::class . '::mapFromDatabase', $records);
    }

    /**
     * @param Query $query
     * @return static[]
     */
    public static function fetchByQuery (Query $query) : array
    {
        return array_map(
            static::class . '::mapFromDatabase',
            DatabaseConnection::getInstance()->fetchResults($query)
        );
    }

    public function save ()
    {
        $connection = DatabaseConnection::getInstance();
        $fields = $this->mapToDatabase();

        if ($this->isNew()) {
            $statement = $connection->insertRecord(static::getTableName(), $fields);
            $this->postInsert($statement);
        } else {
            $pkConditions = $this->getPrimaryKeyConditions();

            $pkFields = array_map(function (ComparisonCondition $condition): string {
                return $condition->column;
            }, $pkConditions);

            // Remove the PK
            $fields = array_diff_key($fields, array_flip($pkFields));

            $query = UpdateQueryBuilder::create()
                ->setTable(static::getTableName())
                ->setFields($fields)
                ->addWhere(...$pkConditions)
                ->build();

            $statement = $connection->preformQuery($query);

            if ($statement->affected_rows > 1) {
                throw new MultipleRowsAffectException();
            }
        }
    }

    public function delete ()
    {
        if ($this->isNew()) {
            throw new LogicException('Cannot delete new entities');
        }

        $connection = DatabaseConnection::getInstance();
        $pkConditions = $this->getPrimaryKeyConditions();

        $rowsAffected = $connection->deleteFromTable(static::getTableName(), $pkConditions);

        if ($rowsAffected === 0) {
            throw new NoRowsAffectedException();
        }

        if ($rowsAffected > 1) {
            throw new MultipleRowsAffectException();
        }
    }

    protected static abstract function mapFromDatabase (array $record): self;

    protected static abstract function getTableName (): string;

    public abstract function isNew (): bool;

    /**
     * Will be called only if isNew() returns false
     *
     * @return ComparisonCondition[]
     */
    protected abstract function getPrimaryKeyConditions (): array;

    /**
     * Should include the PK, will be removed automatically if updating the record
     *
     * @return MysqlParam[] Map of column => param
     */
    protected function mapToDatabase (): array
    {
        throw new LogicException('Saving not implemented for ' . static::class);
    }

    protected function postInsert (mysqli_stmt $statement)
    {
        // Do nothing by default
    }
}