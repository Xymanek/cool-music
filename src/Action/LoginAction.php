<?php
declare(strict_types=1);

namespace Action;

use Auth;
use Database\Exception\NoResultsException;
use Database\MysqlParam;
use Database\WhereClause\ComparisonCondition;
use Entity\User;
use HttpResponse;
use Notification;

class LoginAction extends SimplePageAction
{
    protected static $pathParts = ['auth', 'login'];

    public function execute (): HttpResponse
    {
        if (Auth::getInstance()->getLoggedInUser() !== null) {
            return HttpResponse::redirect(HomeAction::generateUrl());
        }

        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
            $errors = [];

            $username = $_POST['username'];
            $password = $_POST['password'];

            if (empty($username)) {
                $errors[] = 'Please enter username';
            }

            if (empty($password)) {
                $errors[] = 'Please enter password';
            }

            if (count($errors) > 0) {
                $this->queueErrorsNotification($errors);

                return $this->renderView('auth/login.php', [
                    'username' => $username,
                ]);
            }

            try {
                $user = User::fetchOneByCriteria(
                    ComparisonCondition::equals('username', MysqlParam::string($username))
                );
            } catch (NoResultsException $e) {
                // Security: do not reveal that the username was not found
                $this->queueErrorsNotification(['The username/password combination that you entered does not match']);

                return $this->renderView('auth/login.php', [
                    'username' => $username,
                ]);
            }

            if (!password_verify($password, $user->passwordHash)) {
                // Security: do not reveal that the username was not found
                $this->queueErrorsNotification(['The username/password combination that you entered does not match']);

                return $this->renderView('auth/login.php', [
                    'username' => $username,
                ]);
            }

            Auth::getInstance()->setLoggedInUser($user);

            $notification = new Notification();
            $notification->content = 'Successfully logged in';
            $notification->styleSuccess();
            $notification->addToQueue();

            return HttpResponse::redirect(HomeAction::generateUrl());
        }

        return $this->renderView('auth/login.php');
    }
}