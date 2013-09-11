<?php

namespace models;

use \models\UserDatabaseModel as UserDB;

class LoginModel
{
    public function isLoggedIn()
    {
    }

    public function logInUser(UserCredentials $credentials) {}

    public function logOut($user) {}
}
