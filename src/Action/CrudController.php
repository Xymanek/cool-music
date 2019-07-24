<?php
declare(strict_types=1);

namespace Action;

use HttpResponse;

abstract class CrudController extends BaseController
{
    protected function executeWithParts (array $specificPathParts): HttpResponse
    {
        if ($specificPathParts === ['new']) {
            return $this->create();
        }

        $id = (int) $specificPathParts[0];

        if (count($specificPathParts) === 1) {
            // /base/2
            return $this->show($id);
        }

        $action = $specificPathParts[1];

        switch ($action) {
            case 'edit':
                return $this->edit($id);

            case 'delete':
                return $this->delete($id);
        }

        array_shift($specificPathParts);
        array_shift($specificPathParts);

        return $this->customAction($action, $id, $specificPathParts);
    }

    protected function create (): HttpResponse
    {
        throw new NotFoundException(static::class . '::create not implemented');
    }

    protected function show (int $id): HttpResponse
    {
        throw new NotFoundException(static::class . '::show not implemented');
    }

    protected function edit (int $id): HttpResponse
    {
        throw new NotFoundException(static::class . '::edit not implemented');
    }

    protected function delete (int $id): HttpResponse
    {
        throw new NotFoundException(static::class . '::delete not implemented');
    }

    protected function customAction (string $action, int $id, array $additionalParts): HttpResponse
    {
        throw new NotFoundException();
    }

    public static function generateUrlForAction (int $id, string $action, array $additionalParts = []) : string
    {
        if (count($additionalParts) > 0) {
            $additionalParts = '/' . implode('/', $additionalParts);
        } else {
            $additionalParts = '';
        }

        return self::generateUrlNoParts() . "/{$id}/{$action}{$additionalParts}";
    }

    public static function generateUrlForShow (int $id) : string
    {
        return self::generateUrlNoParts() . "/{$id}";
    }

    public static function generateUrlForCreate () : string
    {
        return self::generateUrlNoParts() . "/new";
    }
}