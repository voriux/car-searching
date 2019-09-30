<?php

namespace App\Collector\Polizinginiai;

class TextUtil
{
    /**
     * @param $text
     * @return string
     */
    public function detectTitle($text): string
    {
        $title = explode(",", trim($text));
        return isset($title[0]) ? $title[0]: '';
    }

    /**
     * @param $text
     * @return string
     */
    public function detectImage($text): string
    {
        return $text;
    }

    /**
     * @param $text
     * @return string
     */
    public function detectHref($text): string
    {
        return 'https://www.polizinginiai.lt' . $text;
    }

    /**
     * @param $text
     * @return int
     */
    public function detectPrice($text): int
    {
        $text = trim($text);
        $text = str_replace(['€', ' '], '', $text);
        return (int)$text;
    }

    /**
     * @param $text
     * @return int
     */
    public function detectPower($text): int
    {
        $text = trim($text);
        $regexp = '/(\d*)kW/';
        preg_match($regexp, $text, $matches);
        return (int)$matches[1];
    }

    /**
     * @param $text
     * @return string
     */
    public function detectFuel($text): string
    {
        $text = trim($text);
        return (stripos($text, 'benzinas') !== false)
            ? 'Benzinas'
            : 'Dyzelis';
    }

    /**
     * @param $text
     * @return string
     */
    public function detectBodyType($text): string
    {
        return (stripos($text, 'universalas') !== false)
            ? 'Universalas'
            : 'Sedanas';
    }

    /**
     * @param $text
     * @return int
     */
    public function detectKm($text): int
    {
        return (int)str_replace([' ', 'km'], '', $text);
    }
}
