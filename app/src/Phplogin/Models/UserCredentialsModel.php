<?php

namespace Phplogin\Models;

/**
 * Contains rules of how usernames and passwords are formed
 */
class UserCredentialsModel
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * Creates an object containing properly formed credentials
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        // TODO: Validate forming of strings
        $this->username = $username;
        $this->password = $password;
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
    public function getPassword()
    {
        return $this->password;
    }
}
