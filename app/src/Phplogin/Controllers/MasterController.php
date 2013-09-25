<?php

namespace Phplogin\Controllers;

use Phplogin\Controllers\LoginController;
use Phplogin\Models\UserListModel;
use Phplogin\Models\LoginModel;


/**
 * Controls and starts the entire app,
 * there can only be one in charge so it's static.
 */
class MasterController
{
    public static function run()
    {

        // Open the db and give it to the LoginModel
        $db = new UserListModel('db/users.sqlite');
        $loginModel = new LoginModel($db);

        $ctrl = new LoginController($loginModel);
        $ctrl->indexAction();
    }
}
