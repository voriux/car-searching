<?php

namespace App;

class TextUtil
{
    /**
     * @param $text
     * @return string
     */
    public function detectImage($text, $baseUrl): string
    {
        $pattern = '/background-image:url\((.*)\)/';

        preg_match($pattern, $text, $matches);

        return $baseUrl . $matches[1];
    }

    /**
     * @param $text
     * @return string
     */
    public function detectUrl($text, $baseUrl): string
    {
        return $baseUrl . $text;
    }

    /**
     * @param $text
     * @return string
     */
    public function detectPrice($text): string
    {
        $text = str_replace(" ", "", $text);
        preg_match('/(.*)€[^€]/u', $text, $matches);
        return $matches[1];
    }

    /**
     * @param $text
     * @return string
     */
    public function detectVat($text): string
    {
        $text = str_replace(" ", "", $text);
        preg_match('/\((.*)%PVM\)/', $text, $matches);
        return $matches[1];
    }
}