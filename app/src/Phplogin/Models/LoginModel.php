<?php

namespace Phplogin\Models;

use Phplogin\Models\UserModel;
use Phplogin\Models\ServiceModel;
use Phplogin\Models\UserCredentialsModel;
use Phplogin\Exceptions\NotAuthorizedException;
use Phplogin\Exceptions\NotFoundException;
use Exception;

class LoginModel
{
    /**
     * User-database
     * @var ServiceModel
     */
    private $db;

    /**
     * Key to logged in user in session
     * @var string
     */
    private static $sessionLoggedIn = 'LoginModel::LoggedInUser';

    /**
     * @param ServiceModel $database Database to match the login-attempts to
     */
    public function __construct(ServiceModel $database)
    {
        // TODO: Use helper class instead of ServiceModel
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
     * Returns the name of the sessions logged in user
     * @return string
     */
    public function getLoggedInUsername()
    {
        if (! $this->isLoggedIn()) {
            throw new Exception('No user logged in');
        }
        // TODO: Make sure this is a usermodel
        return $_SESSION[self::$sessionLoggedIn];
    }

    /**
     * Save the user session
     * @param  UserModel $user
     */
    private function persistLogin(UserModel $user)
    {
        // TODO: Check for session theft
        $_SESSION[self::$sessionLoggedIn] = $user->getUsername();

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
