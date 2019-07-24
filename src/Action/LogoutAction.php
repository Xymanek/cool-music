<?php
declare(strict_types=1);

namespace Action;

use Auth;
use HttpResponse;
use Notification;

class LogoutAction extends SimplePageAction
{
    protected static $pathParts = ['auth', 'logout'];

    public function execute (): HttpResponse
    {
        Auth::getInstance()->deAuthCurrentUser();

        $notification = new Notification();
        $notification->content = 'Logged out';
        $notification->styleConfirm();
        $notification->addToQueue();

        return HttpResponse::redirect(HomeAction::generateUrl());
    }
}