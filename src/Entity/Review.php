<?php
declare(strict_types=1);

namespace Entity;

use Database\DatabaseConnection;
use Database\MysqlParam;
use Database\Query;
use Database\SelectQueryBuilder;
use Database\WhereClause\ComparisonCondition;

class Review extends BaseEntity
{
    use AutoIncrementIdTrait;

    /**
     * @var int
     */
    public $trackId;

    /**
     * @var string
     */
    public $userName;

    /**
     * @var string
     */
    public $content;

    /**
     * @var int
     */
    public $rating;

    protected static function mapFromDatabase (array $record): BaseEntity
    {
        $entity = new static();

        $entity->id = $record['id'];
        $entity->userName = $record['user_name'];
        $entity->content = $record['content'];
        $entity->rating = $record['rating'];

        return $entity;
    }

    /**
     * Should include the PK, will be removed automatically if updating the record
     *
     * @return MysqlParam[] Map of column => param
     */
    protected function mapToDatabase (): array
    {
        $record = $this->mapIdToDatabase();

        $record['user_name'] = MysqlParam::string($this->userName);
        $record['track_id'] = MysqlParam::integer($this->trackId);
        $record['content'] = MysqlParam::string($this->content);
        $record['rating'] = MysqlParam::integer($this->rating);

        return $record;
    }

    protected static function getTableName (): string
    {
        return 'reviews';
    }

    public static function getAverageRatingForTrack (Track $track): float
    {
        $sql = 'SELECT AVG(rating) as average_rating FROM reviews where track_id = ?';
        $params = [MysqlParam::integer($track->id)];

        $result = DatabaseConnection::getInstance()
            ->fetchSingleResult(new Query($sql, $params));

        return (float) $result['average_rating'];
    }

    public static function countForUser (User $user): int
    {
        $query = SelectQueryBuilder::create()
            ->setTable(self::getTableName())
            ->addWhere(ComparisonCondition::equals('user_name', MysqlParam::string($user->username)))
            ->build();

        $results = DatabaseConnection::getInstance()->fetchResults($query);

        return count($results);
    }
}