<?php
declare(strict_types=1);

use Database\Exception\NoResultsException;
use Entity\User;

class Auth
{
    const USER_ID_SESSION_KEY = '_logged_in_users';

    /**
     * @var self
     */
    private static $instance;

    /**
     * @var User|null
     */
    private $loggedInUser;

    public static function init ()
    {
        self::$instance = new self();
        self::$instance->loadUserFromSession();
    }

    public static function getInstance (): Auth
    {
        return self::$instance;
    }

    /**
     * @return User|null
     */
    public function getLoggedInUser ()
    {
        return $this->loggedInUser;
    }

    private function loadUserFromSession () : bool
    {
        if (empty($_SESSION[self::USER_ID_SESSION_KEY])) {
            return false;
        }

        $user = $this->loadUserFromDatabase($_SESSION[self::USER_ID_SESSION_KEY]);

        if ($user === null) {
            $this->resetSessionValue();
            return false;
        }

        $this->loggedInUser = $user;
        return true;
    }

    private function loadUserFromDatabase (int $id) : User
    {
        try {
            return User::findById($id);
        } catch (NoResultsException $e) {
            return null;
        }
    }

    private function resetSessionValue ()
    {
        unset($_SESSION[self::USER_ID_SESSION_KEY]);
    }

    public function setLoggedInUser (User $user)
    {
        $_SESSION[self::USER_ID_SESSION_KEY] = $user->id;
        $this->loggedInUser = $user;
    }

    public function deAuthCurrentUser ()
    {
        $this->loggedInUser = null;
        $this->resetSessionValue();
    }
}