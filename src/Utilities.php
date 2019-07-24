<?php
declare(strict_types=1);

final class Utilities
{
    private function __construct ()
    {
    }

    public static function removeFromStringStart (string $str, string $prefix): string
    {
        if (substr($str, 0, strlen($prefix)) == $prefix) {
            $str = substr($str, strlen($prefix));
        }

        return $str;
    }
}