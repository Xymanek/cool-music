<?php
declare(strict_types=1);

namespace Entity;

use Database\MysqlParam;
use Database\Query;
use Router;

class Track extends BaseEntity
{
    use AutoIncrementIdTrait;

    /**
     * @var int
     */
    public $albumId;

    /**
     * @var int
     */
    public $genreId;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $sample;

    protected static function mapFromDatabase (array $record): BaseEntity
    {
        $entity = new static();

        $entity->id = $record['id'];
        $entity->albumId = $record['album_id'];
        $entity->genreId = $record['genre_id'];
        $entity->title = $record['title'];
        $entity->description = $record['description'];
        $entity->sample = $record['sample'];

        return $entity;
    }

    protected static function getTableName (): string
    {
        return 'tracks';
    }

    public function getFullSampleUrl () : string
    {
        return Router::getInstance()->getGlobalPrefix() . Router::ASSETS_PREFIX . $this->sample;
    }

    public static function getRecommendedForUser (string $userName) : array
    {
        $query = new Query(file_get_contents(__DIR__ . '/recommended_tracks.sql'), [
            MysqlParam::string($userName),
            MysqlParam::string($userName),
            MysqlParam::string($userName),
        ]);

        return self::fetchByQuery($query);
    }
}