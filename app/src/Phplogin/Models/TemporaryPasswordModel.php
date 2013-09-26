<?php

namespace Phplogin\Models;

use Phplogin\Models\UserModel;

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
     * If password is not set, generates a new random password.
     * @param string  $temporaryPasswordHash optional passwordhash
     */
    public function __construct($temporaryPasswordHash = null)
    {
        // Set or generate password
        if ($temporaryPasswordHash === null) {
            $this->password = $this->generateRandomPassword();
        } else {
            $this->password = $temporaryPasswordHash;
        }
    }

    /**
     * @param  TemporaryPassword $temporaryPassword to match with
     * @return bool
     */
    public function match(TemporaryPasswordModel $temporaryPassword)
    {
        if ($this->userId !== $temporaryPassword->userId) {
            return false;
        }

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
        $this->userId = $userId;
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
     * Returns userId
     * @return int -1 if not set
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getTemporaryPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string random
     */
    private function generateRandomPassword()
    {
        return md5(uniqid(rand(), true));
    }
}
