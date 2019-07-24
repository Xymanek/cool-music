<?php
declare(strict_types=1);

namespace Action;

use Router;

abstract class SimplePageAction extends BaseAction
{
    protected static $pathParts;

    public function matchRoute (array $pathParts): bool
    {
        return $pathParts == static::$pathParts;
    }

    public static function generateUrl () : string
    {
        return Router::getInstance()->getGlobalPrefix() . implode('/', static::$pathParts);
    }
}