<?php

namespace Phplogin\Models;

use Phplogin\Models\UserDatabaseModel as UserDB;

class LoginModel
{
    public function isLoggedIn()
    {
    }

    public function logInUser(UserCredentials $credentials) {}

    public function logOut($user) {}
}
