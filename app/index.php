<?php
session_start();

// FIXME: All files are missing docblocks
// TODO: Use short version of return types in docblocs, e.g. integer -> int

require_once('./vendor/autoload.php');

use Phplogin\Controllers\LoginController;

// Fire it up!
$loginCtrl = new LoginController();
$loginCtrl->printLoginPage();
