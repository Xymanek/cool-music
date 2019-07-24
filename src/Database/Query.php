<?php
declare(strict_types=1);

namespace Database;

class Query
{
    /**
     * @var string
     */
    public $sql;

    /**
     * @var MysqlParam[]
     */
    public $params;

    public function __construct (string $sql, array $params = [])
    {
        $this->sql = $sql;
        $this->params = $params;
    }
}