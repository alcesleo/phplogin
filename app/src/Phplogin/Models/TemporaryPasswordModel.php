<?php

namespace Phplogin\Models;

class TemporaryPasswordModel
{
    /**
     * @var int
     */
    private $userID;

    /**
     * @var string
     */
    private $password;

    /**
     * If password is not set, generates a new random password.
     * @param int     $userID                ID of the user
     * @param string  $temporaryPasswordHash The temporary password
     */
    public function __construct($userID = -1, $temporaryPasswordHash = '')
    {
        $this->userID = $userID;

        if ($temporaryPasswordHash === '') {
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
        if ($this->userID !== $temporaryPassword->userID) {
            return false;
        }

        if ($this->password !== $temporaryPassword->password) {
            return false;
        }

        return true;
    }

    /**
     * @return string random
     */
    private function generateRandomPassword()
    {
        return md5(uniqid(rand(), true));
    }
}
