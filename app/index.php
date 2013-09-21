<?php
session_start();

// FIXME: All files are missing docblocks
// TODO: Use short version of return types in docblocs, e.g. integer -> int

require_once('./vendor/autoload.php');

use Phplogin\Controllers\LoginController;

// Create the Admin-user
//$db = new UserListModel('db/users.sqlite');
//$db->insertUser(new UserModel('Admin', sha1('Password')));

// Fire it up!
$loginCtrl = new LoginController();
$loginCtrl->printLoginPage();
