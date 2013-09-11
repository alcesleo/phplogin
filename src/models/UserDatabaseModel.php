<?php

namespace models;

use models\UserModel;

/**
 *
 */
class UserDatabaseModel
{
    // Temporary array of users
    // FIXME: Use localStorage or something to store this
    private static $users = array(
        'Admin' => '8be3c943b1609fffbfc51aad666d0a04adf83c9d', // 'Password'
        'userTwo' => 'd50f3d3d525303997d705f86cd80182365f964ed' // 'drowssap'
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
