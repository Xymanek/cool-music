<?php
declare(strict_types=1);

namespace Action;

use Database\Exception\NoResultsException;
use Database\MysqlParam;
use Database\WhereClause\ComparisonCondition;
use Entity\Offer;
use Entity\User;
use HttpResponse;
use Notification;

class RegisterAction extends SimplePageAction
{
    protected static $pathParts = ['auth', 'register'];

    public function execute (): HttpResponse
    {
        $offers = Offer::fetchAll();

        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
            $errors = [];
            $idsToOffers = [];

            foreach ($offers as $offer) {
                $idsToOffers[$offer->id] = $offer;
            }

            $username = $_POST['username'];
            $password = $_POST['password'];
            $offerId = $_POST['offer_id'];

            if (empty($username)) {
                $errors[] = 'Username is required';
            } elseif (strlen($username) < 3) {
                $errors[] = 'Username is too short (min 3 characters)';
            } elseif (strlen($username) > 30) {
                $errors[] = 'Username is too long (max 30 characters)';
            }

            if (empty($password)) {
                $errors[] = 'Password is required';
            } elseif (strlen($password) < 4) {
                $errors[] = 'Password is too short (min 3 characters)';
            }

            if (empty($offerId)) {
                $errors[] = 'Please select an offer';
            } elseif (!isset($idsToOffers[$offerId])) {
                $errors[] = 'Invalid offer selected';
            }

            try {
                $existingUser = User::fetchOneByCriteria(
                    ComparisonCondition::equals('username', MysqlParam::string($username))
                );

                if ($existingUser !== null) {
                    $errors[] = 'Username already in use';
                }
            } catch (NoResultsException $e) {
                // Do nothing
            }

            if (count($errors) > 0) {
                $this->queueErrorsNotification($errors);

                return $this->renderView('auth/register.php', [
                    'offers' => $offers,
                    'username' => $username,
                    'offerId' => $offerId,
                ]);
            }

            $user = new User();

            $user->username = $username;
            $user->currentOfferId = (int) $offerId;
            $user->passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $user->save();

            $notification = new Notification();
            $notification->content = 'Successfully registered';
            $notification->styleSuccess();
            $notification->addToQueue();

            return HttpResponse::redirect(LoginAction::generateUrl());
        }

        return $this->renderView('auth/register.php', [
            'offers' => $offers
        ]);
    }
}