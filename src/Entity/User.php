<?php
declare(strict_types=1);

namespace Entity;

use Database\MysqlParam;

class User extends BaseEntity
{
    use AutoIncrementIdTrait;

    /**
     * @var int
     */
    public $currentOfferId;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $passwordHash;

    protected static function mapFromDatabase (array $record): BaseEntity
    {
        $entity = new static();

        $entity->id = $record['id'];
        $entity->currentOfferId = $record['current_offer_id'];
        $entity->username = $record['username'];
        $entity->passwordHash = $record['password_hash'];

        return $entity;
    }

    protected static function getTableName (): string
    {
        return 'users';
    }

    /**
     * Should include the PK, will be removed automatically if updating the record
     *
     * @return MysqlParam[] Map of column => param
     */
    protected function mapToDatabase (): array
    {
        $record = $this->mapIdToDatabase();

        $record['current_offer_id'] = MysqlParam::integer($this->currentOfferId);
        $record['username'] = MysqlParam::string($this->username);
        $record['password_hash'] = MysqlParam::string($this->passwordHash);

        return $record;
    }
}