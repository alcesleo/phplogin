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
        'userOne' => '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', // 'password'
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

    public static function saveUser(UserModel $user)
    {
        // Save user in cookies?
        // setcookie()
    }

}
