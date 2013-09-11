<?php

namespace models;

use \models\UserModel;
use \models\UserDatabaseModel as UserDB;

class UserSaverModel
{
    private static $key = 'UserSaverModel';

    public function __construct()
    {
        // Make sure session is active
        assert(isset($_SESSION));
    }


    /**
     * @return UserModel
     */
    public function load()
    {
        if ($this->validate()) {
            // This is the worst line of code in human history
            return new UserModel($_SESSION['LoggedInAs'], 'LOLThisIsNotEvenTheCorrectPassword', 1);
        }
        throw new \Exception('Not logged in');
    }

    public function save(UserModel $userModel)
    {
        // FIXME: String dependancies suck

        // Prevent session hijacking
        $_SESSION['UserAgent'] = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['IPAddr'] = $_SERVER['REMOTE_ADDR'];

        // FIXME: Very secure.
        $_SESSION['LoggedInAs'] = $userModel->getUsername();
    }

    /**
     * Validate the session variables to prevent session hijacking
     * @return boolean
     */
    private function validate()
    {
        if ($_SESSION['UserAgent'] != $_SERVER['HTTP_USER_AGENT']) {
            return false;
        }

        if ($_SESSION['IPAddr'] != $_SERVER['REMOTE_ADDR']) {
            return false;
        }

        return true;
    }

}
