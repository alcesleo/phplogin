<?php
namespace Codeception\Module;

// here you can define custom functions for WebGuy

class WebHelper extends \Codeception\Module
{
    public function refreshPage()
    {
        $string = '<script type="text/javascript">';
        $string .= 'window.location = "/"';
        $string .= '</script>';

        echo $string;
    }
}
