<?php

namespace views;

use views\HtmlProducerInterface;

/**
 * Formats dates in different languages.
 */
class DateTimeView
{

    private $locale;
    private $format;

    /**
     * @param string $locale The locale to format the string with, "sv_SE" for Swedish.
     * @param string $format http://php.net/manual/en/function.strftime.php
     */
    public function __construct($locale, $format = '%c')
    {
        $this->locale = $locale;
        $this->format = $format;
    }

    /**
     * Generates a formatted date string.
     *
     * @return string
     */
    public function getHTML()
    {
        // FIXME: Does this need to be set everytime?
        setlocale(LC_ALL, $this->locale);
        $timestamp = strftime($this->format);
        return "<span class='timestamp'>$timestamp</span>";
    }
}
