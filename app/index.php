<?php

require_once('/vendor/autoload.php')

use controllers\LoginController;

//$userOne = new \models\UserModel();

// Fire it up!
$loginCtrl = new LoginController();
echo $loginCtrl->logIn();
