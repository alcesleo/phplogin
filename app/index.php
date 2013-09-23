<?php
session_start();

require_once('./vendor/autoload.php');

use Phplogin\Controllers\LoginController;

// Create the Admin-user
//$db = new UserListModel('db/users.sqlite');
//$db->insertUser(new UserModel('Admin', sha1('Password')));

// Fire it up!
$loginCtrl = new LoginController();
$loginCtrl->printLoginPage();
