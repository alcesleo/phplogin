<?php
namespace Phplogin\Views;

class View
{
    protected static $loginPage = 'login';
    protected static $logoutPage = 'logout';

    /**
     * Redirect to same page without a request
     */
    public static function refresh()
    {
        // Get request without get-variables
        $page = strtok($_SERVER['REQUEST_URI'], '?');
        header("Location: $page");
    }
}
