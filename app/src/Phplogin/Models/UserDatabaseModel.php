<?php

namespace Phplogin\Models;

use Phplogin\Models\UserModel;

/**
 *
 */
class UserDatabaseModel
{
    // Temporary array of users
    private static $users = array(
        'Admin' => '8be3c943b1609fffbfc51aad666d0a04adf83c9d' // 'Password'
    );

    public static function userExists($username)
    {
        return array_key_exists($username, self::$users);
    }

    public static function getPasswordHash($username)
    {
        return self::$users[$username];
    }
}
