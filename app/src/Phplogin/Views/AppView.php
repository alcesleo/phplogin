<?php

namespace Phplogin\Views;

use Phplogin\Views\LoginView;
use Phplogin\Views\DateTimeView;

class AppView
{
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
     * @return string HTML
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
     * Redirects to a page
     * @param string $path Path to redirect to, home if omitted
     */
    public function redirect($path = '/')
    {
        header("Location: $path");
    }
}
