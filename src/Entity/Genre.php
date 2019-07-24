<?php
declare(strict_types=1);

namespace Entity;

class Genre extends BaseEntity
{
    use AutoIncrementIdTrait;

    /**
     * @var string
     */
    public $title;

    protected static function mapFromDatabase (array $record): BaseEntity
    {
        $entity = new static();

        $entity->id = $record['id'];
        $entity->title = $record['title'];

        return $entity;
    }

    protected static function getTableName (): string
    {
        return 'genres';
    }
}