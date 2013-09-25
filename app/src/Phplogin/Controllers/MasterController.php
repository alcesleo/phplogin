<?php

namespace Phplogin\Controllers;

use Phplogin\Controllers\LoginController;

/**
 * Controls and starts the entire app,
 * there can only be one in charge so it's static.
 */
class MasterController
{
    public static function run()
    {
        $ctrl = new LoginController();
        $ctrl->indexAction();
    }
}
