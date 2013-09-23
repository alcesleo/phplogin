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
     * When page is not found, redirect to login-page
     * @var string
     */
    private static $fourOhFour = '/login/printLoginPage';

    /**
     * Create appropriate controller and call requested action.
     *
     * Uses the URL to instantiate the correct controller and  call
     * an action on it. Redirects to 404-page if a controller,
     * or if the action within that controller, is not found.
     */
    public static function dispatch()
    {
        // Base URL.
        // This is checked here because I might want
        // a home-page other than the login-page later
        if ($_SERVER['REQUEST_URI'] == '/') {
            self::redirect(self::$fourOhFour);
        }

        $request = self::getRequestPathArray();

        // Determine controller
        // www.example.com/page/action => 'Phplogin\Controllers\PageController'
        $ctrlName = self::$ctrlNamespace . ucfirst($request[0]) . 'Controller';

        // No controller with that name
        if (! class_exists($ctrlName)) {
            self::redirect(self::$fourOhFour);
        }

        // Determine action
        $actionName = $request[1];

        // No action with that name in specified controller
        if (! method_exists($ctrlName, $actionName)) {
            self::redirect(self::$fourOhFour);
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
