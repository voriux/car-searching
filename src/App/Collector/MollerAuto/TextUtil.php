<?php

namespace App\Collector\MollerAuto;

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
     * @return int
     */
    public function detectPrice($text): int
    {
        $text = str_replace(" ", "", $text);
        preg_match('/(.*)€[^€]/u', $text, $matches);
        return (int)$matches[1];
    }

    /**
     * @param $text
     * @return int
     */
    public function detectVat($text): int
    {
        $text = str_replace(" ", "", $text);
        preg_match('/\((.*)%PVM\)/', $text, $matches);
        return (int)$matches[1];
    }

    /**
     * @param $text
     * @return string
     */
    public function detectProductionYear($text): string
    {
        preg_match('/(\d{2})\/(\d{4}),/', $text, $matches);
        return $matches[2].'-'.$matches[1];
    }

    /**
     * @param $text
     * @return int
     */
    public function detectPower($text): int
    {
        preg_match('/(\d*) Ag/', $text, $matches);
        return round($matches[1] * 0.735499);
    }

    /**
     * @param $text
     * @return string
     */
    public function detectGearbox($text): string
    {
        return (strpos($text, 'aut') !== false)
            ? 'Automatinė'
            : 'Mechaninė';
    }

    /**
     * @param $text
     * @return string
     */
    public function detectBodyType($text): string
    {
        return (strpos($text, 'universalas') !== false)
            ? 'Universalas'
            : 'Sedanas';
    }

    /**
     * @param $text
     * @return string
     */
    public function detectFuel($text): string
    {
        return (strpos($text, 'benzinas') !== false)
            ? 'Benzinas'
            : 'Dyzelis';
    }

    /**
     * @param $text
     * @return string
     */
    public function detectKm($text): string
    {
        return str_replace(['km', ' '], '', $text);
    }

    /**
     * @param $text
     * @return string
     */
    public function detectExternalId($text): string
    {
        $pattern = '/lt\/vehicle\/(\d*)\//';
        preg_match($pattern, $text, $matches);
        return $matches[1];
    }
}