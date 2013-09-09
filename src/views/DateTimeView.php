<?php

namespace views;

use views\HtmlProducerInterface;

/**
 * Formats dates in different languages.
 */
class DateTimeView
{

    private $locale;

    /**
     * @param string $locale The locale to format the string with, "sv_SE" for Swedish.
     */
    public function __construct($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Generates a formatted date string.
     *
     * @param  string $format http://php.net/manual/en/function.strftime.php
     * @return string
     */
    public function getHTML($format = '%c')
    {
        // FIXME: Does this need to be set everytime?
        setlocale(LC_ALL, $this->locale);
        $timestamp = strftime($format);
        return "<span class='timestamp'>$timestamp</span>";
    }
}
