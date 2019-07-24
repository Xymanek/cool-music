<?php
declare(strict_types=1);

namespace Action;

use Auth;
use HttpResponse;

trait RequireLoggedInTrait
{
    public function preExecute ()
    {
        if (Auth::getInstance()->getLoggedInUser() === null) {
            return HttpResponse::redirect(LoginAction::generateUrl());
        }

        return null;
    }
}