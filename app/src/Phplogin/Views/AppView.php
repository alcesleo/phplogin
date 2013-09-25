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

    private $body;

    public function __construct()
    {
        $this->dateTimeView = new DateTimeView('sv_SE.UTF-8', '%A, den %e %B år %Y. Klockan är [%H:%M:%S].');
    }

    // TODO: Use objects instead of html-strings, combine Page-objects...? Merge(array of Page-objects)
    /**
     * Generate the complete html-page
     * @param string $title title of the page
     * @param string $body HTML
     * @return string HTML
     */
    public function getHtml($title = 'PHPLogin')
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
                $this->body
                $dateHTML
            </body>
            </html>
        ";
    }

    public function addBody($body)
    {
        $this->body .= "\n" . $body;
    }
}
