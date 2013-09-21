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
     * @param string $username
     * @param string $passwordHash Optional passwordHash
     */
    public function __construct($username, $passwordHash = null)
    {
        $this->username = $username;
        $this->passwordHash = $passwordHash;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getHash()
    {
        return $this->passwordHash;
    }
}
