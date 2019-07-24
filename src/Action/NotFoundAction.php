<?php
declare(strict_types=1);

namespace Action;

use HttpResponse;
use LogicException;

class NotFoundAction extends BaseAction
{
    public function matchRoute (array $pathParts): bool
    {
        throw new LogicException('matchRoute called on 404 action');
    }

    public function execute (): HttpResponse
    {
        return $this->renderView('errors/404.php', [], 404);
    }
}