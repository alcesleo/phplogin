<?php

namespace Phplogin\Views;

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

        // FIXME: Is this really the way to do this?
        // TODO: Could this be set to LC_TIME?
        setlocale(LC_ALL, $this->locale);
    }

    /**
     * Generates a formatted date string.
     *
     * @return string
     */
    public function getHTML()
    {
        $timestamp = strftime($this->format);
        return "<p class='timestamp'>$timestamp</p>";
    }
}
