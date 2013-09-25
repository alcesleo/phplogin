<?php
session_start();
require_once('./vendor/autoload.php');
Phplogin\Controllers\MasterController::run();
