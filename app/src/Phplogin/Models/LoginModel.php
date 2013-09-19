<?php

namespace Phplogin\Models;

use Phplogin\Models\UserModel;
use Phplogin\Models\UserListModel;
use Phplogin\Models\UserCredentialsModel;
use Exception;
use Phplogin\Exceptions\NotFoundException;

class LoginModel
{
    // TODO: Order functions

    private $db;

    private static $sessionLoggedIn = 'LoginModel::LoggedInUser';

    public function __construct(UserListModel $database)
    {
        $this->db = $database;
    }

    /**
     * Log in a user
     * @param  UserCredentialsModel $credentials
     * @return UserModel // REALLY???
     * @throws Exception If not authorized
     * @throws NotFoundException If user doesn't exist
     */
    public function logIn(UserCredentialsModel $credentials)
    {
        // TODO: Authorize credentials

        // Get user from database
        // NOTE: This can throw NotFoundException
        $user = $this->db->getUserByName($credentials->getUsername());

        // Authorize
        if (! $this->authorize($credentials, $user)) {
            throw new Exception('Not authorized');
        }

        $this->persistLogin($user);

        return $user;
    }

    public function getLoggedInUser()
    {
        if (! $this->isLoggedIn()) {
            throw new Exception('No user logged in');
        }
        return unserialize($_SESSION[self::$sessionLoggedIn]);
    }

    /**
     * Save the user session
     * @param  UserModel $user
     */
    private function persistLogin(UserModel $user)
    {
        // TODO: Check for session theft
        $_SESSION[self::$sessionLoggedIn] = serialize($user);
    }

    private function authorize(UserCredentialsModel $credentials, UserModel $user)
    {
        if ($credentials->getUsername() != $user->getUsername()) {
            return false;
        }

        // FIXME: new Password
        /*
        $dbPass = $user->getPassword();
        if ($dbPass->matchCredentials($credentials))
        */
        // FIXME: Don't encrypt here
        if (sha1($credentials->getPassword()) != $user->getHash()) {
            return false;
        }

        return true;
    }

    public function logOut()
    {
        // TODO: Delete session variables
        unset($_SESSION[self::$sessionLoggedIn]);
    }

    /**
     * If this session is logged in
     * @return bool
     */
    public function isLoggedIn()
    {
        // TODO: May need a closer look...
        return isset($_SESSION[self::$sessionLoggedIn]);
    }

    // TODO: Preliminary, return session|cookie|input?
    public function loggedInBy()
    {

    }
}
