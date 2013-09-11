<?php

require_once('bootstrap.php');

use controllers\LoginController;


//$userOne = new \models\UserModel();

// Fire it up!
$loginCtrl = new LoginController();
echo $loginCtrl->logIn();

