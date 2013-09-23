<?php

namespace Phplogin\Models;

use Phplogin\Models\UserModel;
use Phplogin\Models\UserListModel;
use Phplogin\Models\UserCredentialsModel;
use Phplogin\Exceptions\NotAuthorizedException;
use Phplogin\Exceptions\NotFoundException;
use Exception;

class LoginModel
{
    /**
     * User-database
     * @var UserListModel
     */
    private $db;

    /**
     * Key to logged in user in session
     * @var string
     */
    private static $sessionLoggedIn = 'LoginModel::LoggedInUser';

    /**
     * @param UserListModel $database Database to match the login-attempts to
     */
    public function __construct(UserListModel $database)
    {
        $this->db = $database;
    }

    /**
     * Log in a user with provided credentials
     * @param  UserCredentialsModel $credentials
     * @return UserModel
     * @throws NotAuthorizedException If not authorized, or user doesn't exist
     */
    public function logInWithCredentials(UserCredentialsModel $credentials)
    {
        // Get user from database
        try {
            $user = $this->db->getUserByName($credentials->getUsername());
        } catch (NotFoundException $e) {
            // Do not reveal if the user exists
            throw new NotAuthorizedException();
        }

        // Authorize
        if (! $this->authorizeCredentials($user, $credentials)) {
            throw new NotAuthorizedException();
        }

        // Save in session
        $this->persistLogin($user);

        return $user;
    }

    /**
     * Returns the logged in user
     * @return UserModel
     */
    public function getLoggedInUser()
    {
        if (! $this->isLoggedIn()) {
            throw new Exception('No user logged in');
        }
        // TODO: Make sure this is a usermodel
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


    // TODO: Why is this here?
    private function authorizeCredentials(UserModel $user, UserCredentialsModel $credentials)
    {
        if ($credentials->getUsername() != $user->getUsername()) {
            return false;
        }

        // FIXME: Don't encrypt here
        if (sha1($credentials->getPassword()) != $user->getHash()) {
            return false;
        }

        return true;
    }

    /**
     * Logs the user out
     * @return bool true if successfully logged out
     *              false if not logged in to start with
     */
    public function logOut()
    {
        if ($this->isLoggedIn()) {
            // Delete session variables
            unset($_SESSION[self::$sessionLoggedIn]);
            return true;
        }
        return false;
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
}
