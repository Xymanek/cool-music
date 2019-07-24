<?php
declare(strict_types=1);

class Notification
{
    const SESSION_KEY = '_notifications';

    /**
     * Note that the escaping should be handled where the notification is created
     *
     * @var string
     */
    public $content;

    /**
     * @var string
     */
    public $color;

    /**
     * @var string
     */
    public $backgroundColor;

    public function styleSuccess ()
    {
        $this->color = '#155724';
        $this->backgroundColor = '#d4edda';
    }

    public function styleDanger ()
    {
        $this->color = '#721c24';
        $this->backgroundColor = '#f8d7da';
    }

    public function styleConfirm ()
    {
        $this->color = '#004085';
        $this->backgroundColor = '#cce5ff;';
    }

    public function addToQueue ()
    {
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }

        $_SESSION[self::SESSION_KEY][] = $this;
    }

    /**
     * @return self[]
     */
    public static function consumeAllQueued () : array
    {
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
            return [];
        }

        $notifications = $_SESSION[self::SESSION_KEY];
        $_SESSION[self::SESSION_KEY] = [];

        return $notifications;
    }
}