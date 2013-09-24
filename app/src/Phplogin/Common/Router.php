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
    private static $fourOhFour = '/login';

    /**
     * Action called when no action is specified
     * @var string
     */
    private static $defaultAction = 'indexAction';

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

        $ctrlName = self::getControllerName($request[0]);

        // No controller with that name
        if (! class_exists($ctrlName)) {
            self::redirect(self::$fourOhFour);
        }

        // Determine action
        $request[1] = isset($request[1]) ? $request[1] : '';
        $actionName = self::getActionName($request[1]);

        // No action with that name in specified controller
        if (! method_exists($ctrlName, $actionName)) {
            self::redirect(self::$fourOhFour);
        }

        // Call the action
        $ctrl = new $ctrlName();
        $ctrl->$actionName();
    }

    /**
     * 'example' => 'Namespace\ExampleController'
     * @param  string $name name of controller
     * @return string       full reference to controller
     */
    private static function getControllerName($name)
    {
        // Determine controller
        // www.example.com/page/action => 'Phplogin\Controllers\PageController'
        return self::$ctrlNamespace . ucfirst($name) . 'Controller';
    }

    /**
     * 'example' => exampleAction'
     * '' => 'indexAction'
     * @param  string $name name of action
     * @return string       full name of action
     */
    private static function getActionName($name)
    {
        return strlen($name) == 0 ? self::$defaultAction : $name . 'Action';
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
