<?php
declare(strict_types=1);

namespace Entity;

use Database\MysqlParam;
use Database\WhereClause\ComparisonCondition;
use mysqli_stmt;

trait AutoIncrementIdTrait
{
    /**
     * @var int
     */
    public $id;

    /**
     * @param int $id
     * @return self
     *
     * @throws \Database\Exception\MultipleResultsException
     * @throws \Database\Exception\NoResultsException
     */
    public static function findById (int $id): self
    {
        return static::fetchOneByCriteria(new ComparisonCondition(
            'id',
            '=',
            MysqlParam::integer($id)
        ));
    }

    public function isNew (): bool
    {
        return $this->id === null;
    }

    /**
     * Will be called only if isNew() returns false
     *
     * @return ComparisonCondition[]
     */
    protected function getPrimaryKeyConditions (): array
    {
        return [
            ComparisonCondition::equals('id', MysqlParam::integer($this->id))
        ];
    }

    protected function mapIdToDatabase () : array
    {
        return [
            'id' => $this->isNew() ? MysqlParam::null() : MysqlParam::integer($this->id)
        ];
    }

    protected function postInsert (mysqli_stmt $statement)
    {
        $this->id = $statement->insert_id;
    }
}