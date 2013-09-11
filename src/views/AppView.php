<?php

namespace views;

use \views\LoginView;
use \views\DateTimeView;

class AppView
{
    private static $loginPage = 'login';
    private static $logoutPage = 'logout';

    public function getHTML($loginHTML, $dateHTML)
    {
        $title = 'PHPLogin';

        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='utf-8'>
                <title>$title</title>
            </head>
            <body>
                <h1>PHPLogin</h1>
                $loginHTML
                $dateHTML
            </body>
            </html>
        ";
    }

    public function userWantsToLogIn()
    {
        return isset($_GET[self::$loginPage]);
    }

    public function userWantsToLogOut()
    {
        return isset($_GET[self::$logoutPage]);
    }
}
