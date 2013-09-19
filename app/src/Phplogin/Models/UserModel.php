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
    private $hash;

    /**
     * @param string $username
     * @param string $hash     encrypted password
     */
    public function __construct($username, $hash)
    {
        $this->username = $username;
        $this->hash = $hash;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getHash()
    {
        return $this->hash;
    }
}
