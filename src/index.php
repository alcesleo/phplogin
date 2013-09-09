<?php

use controllers\LoginController;

/**
 * Autoload classes
 *
 * @param string $class
 * @return void
 */
function autoload($class)
{
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    require_once($file);
}
spl_autoload_register('autoload');

// Fire it up!
$loginCtrl = new LoginController();
echo $loginCtrl->logIn();
