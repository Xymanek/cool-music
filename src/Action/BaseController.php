<?php
declare(strict_types=1);

namespace Action;

use HttpResponse;
use LogicException;
use Router;

/**
 * A controller is an action that can optionally have parts in URL after the base
 */
abstract class BaseController extends BaseAction
{
    /**
     * @var string[]
     */
    protected static $baseParts;

    public function matchRoute (array $pathParts): bool
    {
        if (count($pathParts) < count(static::$baseParts)) {
            return false;
        }

        for ($i = 0; $i < count(static::$baseParts); $i++) {
            if ($pathParts[$i] !== static::$baseParts[$i]) {
                return false;
            }
        }

        return true;
    }

    public function execute (): HttpResponse
    {
        $pathParts = Router::getInstance()->getPathParts();

        for ($i = 0; $i < count(static::$baseParts); $i++) {
            array_shift($pathParts);
        }

        return $this->executeInternal($pathParts);
    }

    protected function executeInternal (array $specificPathParts) : HttpResponse
    {
        if (count($specificPathParts) > 0) {
            return $this->executeWithParts($specificPathParts);
        }

        return $this->executeNoParts();
    }

    protected function executeNoParts () : HttpResponse
    {
        throw new LogicException('executeNoParts is not implemented');
    }

    protected function executeWithParts (array $specificPathParts) : HttpResponse
    {
        throw new LogicException('executeWithParts is not implemented');
    }

    public static function generateUrlNoParts () : string
    {
        return Router::getInstance()->getGlobalPrefix() . implode('/', static::$baseParts);
    }
}