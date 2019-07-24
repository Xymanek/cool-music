<?php
declare(strict_types=1);

namespace Entity;

use Database\Exception\NoResultsException;
use Database\MysqlParam;
use Database\WhereClause\ComparisonCondition;

class Playlist extends BaseEntity
{
    use AutoIncrementIdTrait;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var string
     */
    public $title;

    public function hasTrack (int $trackId): bool
    {
        try {
            TrackInPlaylist::fetchOneByCriteria(
                ComparisonCondition::equals('playlist_id', MysqlParam::integer($this->id)),
                ComparisonCondition::equals('track_id', MysqlParam::integer($trackId))
            );
        } catch (NoResultsException $e) {
            return false;
        }

        return true;
    }

    protected static function mapFromDatabase (array $record): BaseEntity
    {
        $entity = new static();

        $entity->id = $record['id'];
        $entity->userId = $record['user_id'];
        $entity->title = $record['title'];

        return $entity;
    }

    protected static function getTableName (): string
    {
        return 'playlists';
    }

    /**
     * Should include the PK, will be removed automatically if updating the record
     *
     * @return MysqlParam[] Map of column => param
     */
    protected function mapToDatabase (): array
    {
        $record = $this->mapIdToDatabase();

        $record['user_id'] = MysqlParam::integer($this->userId);
        $record['title'] = MysqlParam::string($this->title);

        return $record;
    }
}