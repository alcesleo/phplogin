<?php

namespace Phplogin\Views;

use Phplogin\Views\LoginView;
use Phplogin\Views\DateTimeView;

class AppView
{
    // TODO: How do getFormHTML know about these?
    private static $loginPage = 'login';
    private static $logoutPage = 'logout';

    /**
     * @var DateTimeView
     */
    private $dateTimeView;

    public function __construct()
    {
        $this->dateTimeView = new DateTimeView('sv_SE.UTF-8', '%A, den %e %B år %Y. Klockan är [%H:%M:%S].');
    }

    /**
     * Generate the complete html-page
     * @param string $title title of the page
     * @param string $body HTML
     */
    public function getHTML($body, $title = 'PHPLogin')
    {
        $dateHTML = $this->dateTimeView->getHTML();

        return "<!DOCTYPE html>
            <html>
            <head>
                <meta charset='utf-8'>
                <title>$title</title>
            </head>
            <body>
                <h1>PHPLogin</h1>
                $body
                $dateHTML
            </body>
            </html>
        ";
    }

    /**
     * If loginpage is active
     * @return bool
     */
    public function userWantsToLogIn()
    {
        return isset($_GET[self::$loginPage]);
    }

    /**
     * If logoutpage is active
     * @return bool
     */
    public function userWantsToLogOut()
    {
        return isset($_GET[self::$logoutPage]);
    }
}
