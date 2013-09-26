<?php

namespace Phplogin\Models;

class UserModel
{

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $passwordHash;

    /**
     * @var int
     */
    private $userId;

    /**
     * @param string $username
     * @param string $passwordHash Optional passwordHash
     */
    public function __construct($username, $passwordHash = null, $userId = -1)
    {
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string 40 char encrypted password
     */
    public function getHash()
    {
        return $this->passwordHash;
    }

    public function getUserId()
    {
        return $this->userId;
    }
}
