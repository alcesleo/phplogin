<?php

namespace Phplogin\Common;

/**
 * This class handles the URL, redirecting to pages, and based on that URL calling the appropriate controller->action.
 */
class Router
{
    /**
     * Namespace of controllers to handle requests
     * @var string
     */
    private static $ctrlNamespace = 'Phplogin\\Controllers\\';

    /**
     * Use the URL to dispatch the correct controller and action
     */
    public static function dispatch()
    {
        // If base request
        if ($_SERVER['REQUEST_URI'] == '/') {
            // TODO: Redirect
            return;
        }

        $request = self::getRequestPathArray();

        // Get controller
        $ctrlName = self::$ctrlNamespace . ucfirst($request[0]) . 'Controller';

        if (! class_exists($ctrlName)) {
            // TODO: Redirect to not found
            assert(false);
        }

        // Get action
        // FIXME: This should be more robust
        $actionName = $request[1];

        if (! method_exists($ctrlName, $actionName)) {
            // TODO: Redirect to not found
            assert(false);
        }

        // Call the action
        $ctrl = new $ctrlName();
        $ctrl->$actionName();
    }

    /**
     * The request path as an array separated by '/'.
     *
     * When the URL is www.example.com/page/action?var=value
     * this function will output array('page', 'action').
     *
     * @return string[]
     */
    private static function getRequestPathArray()
    {
        // On the URL www.example.com/page/action?var=value...

        // Get request-string without query: '/page/action'
        $ret = strtok($_SERVER['REQUEST_URI'], '?');

        // Make array: array('', 'page', 'action')
        $ret = explode('/', $ret);

        // Remove empty index: array('page', 'action')
        array_shift($ret);

        return $ret;
    }

    /**
     * Redirect to a page
     * @param  string $url redirect page
     */
    public static function redirect($url = '/')
    {
        header("Location: $url");
    }
}
