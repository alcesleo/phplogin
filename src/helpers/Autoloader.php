<?php

namespace helpers;

class Autoloader
{
    public function __construct()
    {
        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * Autoload classes
     *
     * @param string $class
     * @return void
     */
    function autoload($class)
    {
        $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        require($file);
    }
}
