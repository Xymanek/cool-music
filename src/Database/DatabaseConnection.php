<?php
declare(strict_types=1);

namespace Database;

use Database\Exception\MultipleResultsException;
use Database\Exception\NoResultsException;
use Database\WhereClause\WhereClauseCondition;
use mysqli;
use mysqli_result;
use mysqli_stmt;
use RuntimeException;
use Throwable;

class DatabaseConnection
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * @var DatabaseConfig
     */
    private $config;

    /**
     * @var mysqli
     */
    private $mysqli;

    public static function init ()
    {
        self::$instance = new self(DatabaseConfig::getInstance());
    }

    public static function getInstance (): DatabaseConnection
    {
        return self::$instance;
    }

    public function __construct (DatabaseConfig $config)
    {
        $this->config = $config;
        $this->initMysqli();
    }

    private function initMysqli ()
    {
        $this->mysqli = new mysqli(
            $this->config->host, $this->config->username, $this->config->password, $this->config->database
        );

        if ($this->mysqli->connect_errno) {
            throw new RuntimeException("Failed to connect to database");
        }
    }

    public function quoteIdentifier (string $identifier): string
    {
        return "`$identifier`";
    }

    public function transactional (callable $func)
    {
        $this->mysqli->begin_transaction();

        try {
            $func();
        } catch (Throwable $e) {
            $this->mysqli->rollback();
            throw $e;
        }

        if (!$this->mysqli->commit()) {
            $this->mysqli->rollback();

            throw new RuntimeException('Failed to commit transaction');
        }
    }

    /**
     * @param Query $query
     * @return array
     *
     * @throws MultipleResultsException
     * @throws NoResultsException
     * @throws RuntimeException
     */
    public function fetchSingleResult (Query $query): array
    {
        $results = $this->fetchResults($query);
        $num = count($results);

        if ($num === 0) {
            throw new NoResultsException('Expected 1 result, got 0');
        }

        if ($num > 1) {
            throw new MultipleResultsException("Expected 1 result, got $num");
        }

        return $results[0];
    }

    /**
     * @param Query $query
     * @return array
     */
    public function fetchResults (Query $query): array
    {
        $statement = $this->preformQuery($query);
        $mysqlResult = $statement->get_result();

        if (!$mysqlResult instanceof mysqli_result) {
            $statement->close();
            throw new RuntimeException('$statement->get_result() failed');
        }

        return $mysqlResult->fetch_all(MYSQLI_ASSOC);
    }

    public function insertRecord (string $table, array $fields): mysqli_stmt
    {
        $fieldNames = [];
        $valuePlaceholders = [];
        $params = [];

        foreach ($fields as $fieldName => $param) {
            $fieldNames[] = $this->quoteIdentifier($fieldName);
            $valuePlaceholders[] = '?';
            $params[] = $param;
        }

        $table = $this->quoteIdentifier($table);
        $fieldNames = implode(',', $fieldNames);
        $valuePlaceholders = implode(',', $valuePlaceholders);

        $query = new Query("INSERT INTO $table ({$fieldNames}) VALUES ({$valuePlaceholders})", $params);
        $statement = $this->preformQuery($query);

        if ($statement->affected_rows !== 1) {
            throw new RuntimeException("INSERT query affected {$statement->affected_rows} rows");
        }

        return $statement;
    }

    /**
     * Removes rows from table based on conditions.
     * Returns the number of removed rows
     *
     * @param string                 $table
     * @param WhereClauseCondition[] $conditions
     * @return int
     * @throws RuntimeException
     */
    public function deleteFromTable (string $table, array $conditions): int
    {
        $params = [];

        /** @noinspection SqlWithoutWhere */
        $sql = 'DELETE FROM ' . $this->quoteIdentifier($table);
        $sql .= SelectQueryBuilder::buildWhereClause($conditions, $params);

        $query = new Query($sql, $params);
        $statement = $this->preformQuery($query);

        return $statement->affected_rows;
    }

    public function preformQuery (Query $query): mysqli_stmt
    {
        $statement = $this->prepareStatement($query->sql);
        $this->bindParams($statement, $query->params);
        $success = $statement->execute();

        if (!$success) {
            throw new RuntimeException('Failed to execute MySQL query: ' . $statement->error);
        }

        return $statement;
    }

    private function prepareStatement (string $query): mysqli_stmt
    {
        $statement = $this->mysqli->prepare($query);

        if (!$statement instanceof mysqli_stmt) {
            throw new RuntimeException('mysqli->prepare() failed');
        }

        return $statement;
    }

    /**
     * @param mysqli_stmt  $statement
     * @param MysqlParam[] $params
     */
    private function bindParams (mysqli_stmt $statement, array $params)
    {
        if (count($params) === 0) {
            // Nothing to do
            return;
        }

        $types = array_reduce(
        // First we fetch the type string from the decorator object
            array_map(function (MysqlParam $param): string {
                return $param->getType();
            }, $params),

            // Then we concatenate them together
            function (string $carry, string $item): string {
                return $carry . $item;
            },

            // We start with an empty string
            ''
        );

        $values = array_map(function (MysqlParam $param) {
            return $param->getValue();
        }, $params);

        $statement->bind_param($types, ...$values);
    }
}