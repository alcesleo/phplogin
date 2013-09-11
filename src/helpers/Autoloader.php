<?php

namespace helpers;

class AutoLoader
{
    /**
     * Path to root of includes
     * @var string path
     */
    private $root;

    /**
     * Register autoloader
     */
    public function __construct($path)
    {
        $this->root = $path;
    }

    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Autoload classes
     *
     * @param string $class
     */
    private function loadClass($class)
    {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        require($this->root . $path);
    }
}
