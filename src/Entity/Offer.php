<?php
declare(strict_types=1);

namespace Entity;

use Database\MysqlParam;
use Router;

class Offer extends BaseEntity
{
    use AutoIncrementIdTrait;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var double
     */
    public $price;

    /**
     * @var string
     */
    public $image;

    public function getFullImageUrl ()
    {
        return Router::getInstance()->getGlobalPrefix() . Router::ASSETS_PREFIX . $this->image;
    }

    protected static function mapFromDatabase (array $record): BaseEntity
    {
        $entity = new static();

        $entity->id = $record['id'];
        $entity->title = $record['title'];
        $entity->description = $record['description'];
        $entity->price = $record['price'];
        $entity->image = $record['image'];

        return $entity;
    }

    protected static function getTableName (): string
    {
        return 'offers';
    }

    /**
     * Should include the PK, will be removed automatically if updating the record
     *
     * @return MysqlParam[] Map of column => param
     */
    protected function mapToDatabase (): array
    {
        $record = $this->mapIdToDatabase();

        $record['title'] = MysqlParam::string($this->title);
        $record['description'] = MysqlParam::string($this->description);
        $record['price'] = MysqlParam::string((string) $this->price);
        $record['image'] = MysqlParam::string($this->image);

        return $record;
    }
}