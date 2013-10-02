<?php

namespace Phplogin\Models;

use Phplogin\Models\UserModel;
use Exception;

class TemporaryPasswordModel
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $password;

    /**
     * @var int
     */
    private $expiration;

    /**
     * Matches the PASSWORD with another TemporaryPasswordModel
     *
     * @param  TemporaryPassword $temporaryPassword to match with
     * @return bool
     */
    public function match(TemporaryPasswordModel $temporaryPassword)
    {
        if ($this->password !== $temporaryPassword->password) {
            return false;
        }
        return true;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = intval($userId);
    }

    /**
     * Returns userId
     * @return int -1 if not set
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @param UserModel $user
     */
    public function setUser(UserModel $user)
    {
        $this->setUsername($user->getUsername());
        $this->setUserId($user->getUserId());
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getTemporaryPassword()
    {
        return $this->password;
    }

    /**
     * @param string $hash 40 char encrypted password
     */
    public function setTemporaryPassword($hash)
    {
        $this->password = $hash;
    }

    /**
     * @param int $time
     */
    public function setExpirationTime($time)
    {
        $this->expiration = intval($time);
    }

    /**
     * @return int
     */
    public function getExpirationTime()
    {
        return $this->expiration;
    }

    /**
     * @return boolean true if expired, false if valid
     * @throws Exception If no expiration time is set
     */
    public function isExpired()
    {
        if (! is_int($this->expiration)) {
            throw new Exception('Expiration time not valid');
        }
        return time() > $this->expiration;
    }

    /**
     * Generates a random password for itself
     */
    public function generateRandomPassword()
    {
        $this->password = md5(uniqid(rand(), true));
    }
}
