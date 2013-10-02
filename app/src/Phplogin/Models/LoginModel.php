<?php

namespace Phplogin\Models;

use Exception;
use Phplogin\Exceptions\NotAuthorizedException;
use Phplogin\Exceptions\NotFoundException;
use Phplogin\Models\EncryptionModel;
use Phplogin\Models\ServiceModel;
use Phplogin\Models\TemporaryPasswordModel;
use Phplogin\Models\UserModel;
use Phplogin\Models\UserCredentialsModel;

class LoginModel
{
    /**
     * User-database
     * @var ServiceModel
     */
    private $service;

    /**
     * Key to logged in user in session
     * @var string
     */
    private static $sessionLoggedIn = 'LoginModel::LoggedInUser';

    /**
     * @param ServiceModel $database Database to match the login-attempts to
     */
    public function __construct(ServiceModel $service)
    {
        $this->service = $service;
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
            $user = $this->service->getUserByName($credentials->getUsername());
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
     * Log in a user with saved credentials
     * @param  TemporaryPasswordModel $temp
     * @return UserModel
     * @throws NotAuthorizedException If not authorized, or user doesn't exist
     */
    public function logInWithTemporaryPassword(TemporaryPasswordModel $temp)
    {
        try {
            // Get user from database
            $user = $this->service->getUserByName($temp->getUsername());
            // Get temporary password from database
            $savedPw = $this->service->getTemporaryPasswordById($user->getUserId());
        } catch (NotFoundException $e) {
            // Do not reveal if the user exists
            throw new NotAuthorizedException();
        }

        // Authorize
        if (! $this->authorizeTemporaryPassword($savedPw, $temp)) {
            throw new NotAuthorizedException();
        }

        $this->persistLogin($user);

        return $user;
    }

    /**
     * Generate and save a temporary password on the server
     * @param  UserModel              $user The user the password should belong to
     * @return TemporaryPasswordModel       The temporary password to save on the client
     */
    public function generateTemporaryPassword(UserModel $user)
    {
        // Generate temporary password
        $temppw = new TemporaryPasswordModel();
        $temppw->generateRandomPassword();
        $temppw->setUser($user);
        $temppw->setExpirationTime(time() + 60); // TODO: Set this dynamically

        // Save on server
        $this->service->saveTemporaryPassword($temppw);

        return $temppw;
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
        return $this->getLoggedInUser()->getUsername();
    }

    /**
     * @return int
     */
    public function getLoggedInUserId()
    {
        return $this->getLoggedInUser()->getUserId();
    }

    /**
     * @return UserModel
     */
    private function getLoggedInUser()
    {
        // TODO: Validate as user object
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


    /**
     * @param  UserModel            $user        to match with
     * @param  UserCredentialsModel $credentials to authorize
     * @return bool
     */
    private function authorizeCredentials(UserModel $user, UserCredentialsModel $credentials)
    {
        // Check username match
        if ($credentials->getUsername() != $user->getUsername()) {
            return false;
        }

        // Check password match
        if (EncryptionModel::encrypt($credentials->getPassword()) != $user->getHash()) {
            return false;
        }

        return true;
    }

    /**
     * @param  TemporaryPasswordModel $fromServer
     * @param  TemporaryPasswordModel $fromClient
     * @return bool
     */
    private function authorizeTemporaryPassword(TemporaryPasswordModel $fromServer, TemporaryPasswordModel $fromClient)
    {
        // Validate timestamp
        if ($fromServer->isExpired()) {
            return false;
        }
        return $fromServer->match($fromClient);
    }

    /**
     * Logs the user out
     * @return bool true if successfully logged out
     *              false if not logged in to start with
     */
    public function logOut()
    {
        if ($this->isLoggedIn()) {
            // Remove from db
            $this->service->deleteTemporaryPassword($this->getLoggedInUserId());

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
