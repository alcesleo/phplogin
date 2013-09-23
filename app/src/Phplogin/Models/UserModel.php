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
        // TODO: What if password is undefined?
        return $this->passwordHash;
    }
}
