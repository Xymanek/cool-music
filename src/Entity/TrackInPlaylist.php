<?php
declare(strict_types=1);

namespace Entity;

use Database\MysqlParam;
use Database\WhereClause\ComparisonCondition;
use mysqli_stmt;

/**
 * Join table for track-playlist many-to-many relation
 */
class TrackInPlaylist extends BaseEntity
{
    /**
     * @var int
     */
    public $playlistId;

    /**
     * @var int
     */
    public $trackId;

    /**
     * @var bool
     */
    private $new = true;

    protected static function mapFromDatabase (array $record): BaseEntity
    {
        $entity = new static();

        $entity->playlistId = $record['playlist_id'];
        $entity->trackId = $record['track_id'];
        $entity->new = false;

        return $entity;
    }

    protected static function getTableName (): string
    {
        return 'track_in_playlist';
    }

    public function isNew (): bool
    {
        return $this->new;
    }

    protected function postInsert (mysqli_stmt $statement)
    {
        $this->new = false;
    }

    /**
     * Will be called only if isNew() returns false
     *
     * @return ComparisonCondition[]
     */
    protected function getPrimaryKeyConditions (): array
    {
        return [
            ComparisonCondition::equals('playlist_id', MysqlParam::integer($this->playlistId)),
            ComparisonCondition::equals('track_id', MysqlParam::integer($this->trackId)),
        ];
    }

    /**
     * Should include the PK, will be removed automatically if updating the record
     *
     * @return MysqlParam[] Map of column => param
     */
    protected function mapToDatabase (): array
    {
        $record = [];

        $record['playlist_id'] = MysqlParam::integer($this->playlistId);
        $record['track_id'] = MysqlParam::integer($this->trackId);

        return $record;
    }
}