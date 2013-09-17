<?php
session_start();

// FIXME: All files are missing docblocks

require_once('./vendor/autoload.php');

use Phplogin\Controllers\LoginController;

// Fire it up!
$loginCtrl = new LoginController();
echo $loginCtrl->logIn();
