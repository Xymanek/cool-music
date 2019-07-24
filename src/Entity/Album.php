<?php
declare(strict_types=1);

namespace Entity;

use Router;

class Album extends BaseEntity
{
    use AutoIncrementIdTrait;

    /**
     * @var int
     */
    public $artistId;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $image;

    /**
     * @var string
     */
    public $thumbnail;

    protected static function mapFromDatabase (array $record): BaseEntity
    {
        $entity = new static();

        $entity->id = $record['id'];
        $entity->artistId = $record['artist_id'];
        $entity->title = $record['title'];
        $entity->image = $record['image'];
        $entity->thumbnail = $record['thumbnail'];

        return $entity;
    }

    protected static function getTableName (): string
    {
        return 'albums';
    }

    public function getFullImageUrl () : string
    {
        return Router::getInstance()->getGlobalPrefix() . Router::ASSETS_PREFIX . $this->image;
    }

    public function getFullThumbnailUrl () : string
    {
        return Router::getInstance()->getGlobalPrefix() . Router::ASSETS_PREFIX . $this->thumbnail;
    }
}