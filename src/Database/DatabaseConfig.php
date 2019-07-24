<?php
declare(strict_types=1);

namespace Database;

use LogicException;

final class DatabaseConfig
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * @var string
     */
    public $host;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $database;

    /**
     * @return DatabaseConfig
     */
    public static function init () : self {
        if (self::$instance !== null) {
            throw new LogicException(__CLASS__ . "::" . __METHOD__ . " called when config is already set");
        }

        return self::$instance = new self();
    }

    public static function hasInstance () : bool {
        return self::$instance !== null;
    }

    public static function getInstance (): DatabaseConfig
    {
        if (!self::hasInstance()) {
            throw new LogicException(self::class . " is not initialized");
        }

        return self::$instance;
    }
}