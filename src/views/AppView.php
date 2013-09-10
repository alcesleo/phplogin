<?php

namespace views;

use \views\LoginView;
use \views\DateTimeView;

class AppView
{
    // TODO: Remove these in LoginView
    private static loginPage = 'login';
    private static logoutPage = 'logout';

    public function getHTML(LoginView $loginView, DateTimeView $dateTimeView)
    {
        $title = 'PHPLogin';
        $loginHTML = $loginView->getHTML();
        $dateHTML = $dateTimeView->getHTML();

        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='utf-8'>
                <title>$title</title>
            </head>
            <body>
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
