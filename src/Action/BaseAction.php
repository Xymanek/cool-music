<?php
declare(strict_types=1);

namespace Action;

use HttpResponse;
use Notification;
use View\ViewEngine;

abstract class BaseAction
{
    public abstract function matchRoute (array $pathParts): bool;

    /**
     * If this returns an HttpResponse then it will be sent to client and execute will not be called
     * @return HttpResponse|null
     */
    public function preExecute() {
        return null;
    }

    public abstract function execute() : HttpResponse;

    protected function renderView (string $template, array $vars = [], $responseCode = 200)
    {
        $response = new HttpResponse();

        $response->content = ViewEngine::getInstance()->render($template, $vars);
        $response->code = $responseCode;

        return $response;
    }

    protected function queueErrorsNotification (array $errors)
    {
        $notification = new Notification();

        $notification->styleDanger();
        $notification->content = ViewEngine::getInstance()->render('shared/form_errors_notification.php', [
            'errors' => $errors,
        ]);

        $notification->addToQueue();
    }
}