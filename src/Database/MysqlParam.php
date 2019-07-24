<?php
declare(strict_types=1);

namespace Database;

final class MysqlParam
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed
     */
    private $value;

    // Use static functions
    private function __construct (string $type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getType (): string
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValue ()
    {
        return $this->value;
    }

    public static function string (string $value)
    {
        return new self('s', $value);
    }

    public static function integer (int $value)
    {
        return new self('i', $value);
    }

    public static function bool (bool $value)
    {
        return new self('i', $value);
    }

    public static function null ()
    {
        return new self('s', null);
    }
}