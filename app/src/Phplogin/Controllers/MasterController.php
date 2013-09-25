<?php

namespace Phplogin\Controllers;

use Phplogin\Controllers\LoginController;
use Phplogin\Models\UserListModel;
use Phplogin\Models\LoginModel;
use PDO;


/**
 * Controls and starts the entire app,
 * there can only be one in charge so it's static.
 */
class MasterController
{
    /**
     * PDO access string
     * @var string
     */
    private static $dbConnectionString = 'sqlite:db/users.sqlite';

    public static function run()
    {
        // Connect to database
        $pdo = new PDO(self::$dbConnectionString);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Open the db and give it to the LoginModel
        $userListModel = new UserListModel($pdo);
        $loginModel = new LoginModel($userListModel);

        // Launch the, for now, only other controller
        $ctrl = new LoginController($loginModel);
        $ctrl->indexAction();
    }

}
