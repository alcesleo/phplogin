<?php

namespace models;

use models\UserDatabaseModel as UserDB;

class UserModel
{
    // Authorization levels
    const NOT_AUTHORIZED = 0;
    const AUTHORIZED_BY_USER = 1;
    const AUTHORIZED_BY_COOKIES = 2;
    const AUTHORIZED_BY_SESSION = 3;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * Indicates if, and how a user is logged in using the constants
     * @var integer
     */
    private $authorization;

    public function __construct($username, $password, $authorization = self::NOT_AUTHORIZED)
    {
        $this->setUsername($username);
        $this->setPassword($password);

        $this->authorization = $authorization;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        if (strlen($password) < 8) {
            throw new \Exception('Password is too short.');
        }

        $this->password = $password;
    }

    public function getHash()
    {
        return sha1($this->password);
    }

    public function isAuthorized()
    {
        return $this->authorization > 0;
    }

    /**
     * @param integer $authLevel
     */
    public function setAuthorization($authLevel)
    {
        $this->authorization = $authLevel;
    }

    public function getAutLevel()
    {
        return $this->authorization;
    }

    public static function authorizeUserWithHash($username, $hash, $authBy = 1)
    {
        // If user exists
        if (! UserDB::userExists($username)) {
            throw new \Exception('User does not exist');
        }

        // And is authorized
        if (! UserDB::getPasswordHash($username) === $hash) {
            throw new \Exception('Username and password do not match');
        }

        // Return new authenticated user
        return new UserModel($username, 'IReallyHopeThisIsntNeeded', $authBy);
    }

    /**
     * Authorizes a user by matching the username against the password hash
     * @param  string $username
     * @param  string $password
     * @return UserModel
     */
    public static function authorizeUser($username, $password, $authBy = 1)
    {
        return self::authorizeUserWithHash($username, sha1($password), $authBy);
    }
}
