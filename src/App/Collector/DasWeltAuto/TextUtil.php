<?php

namespace App\Collector\DasWeltAuto;

class TextUtil
{
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
        preg_match('/(\d*)%/', $text, $matches);
        return (int)$matches[1];
    }

    /**
     * @param $text
     * @return string
     */
    public function detectProductionYear($text): string
    {
        $text = str_replace("Pirma reg.:", "", $text);
        $parts = explode("/", $text);
        return implode('-', [$parts[1], $parts[0]]);
    }

    /**
     * @param $text
     * @return int
     */
    public function detectPower($text): int
    {
        $text = str_replace("Galia:", "", $text);
        return round((int)$text * 0.735499);
    }

    /**
     * @param $text
     * @return int
     */
    public function detectKm($text): int
    {
        $text = str_replace(["Rida:", ' '], "", $text);
        return (int)$text;
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
    public function detectHref($text): string
    {
        return 'https://www.dasweltauto.lt' . $text;
    }

    /**
     * @param $text
     * @return string
     */
    public function detectGearbox($text): string
    {
        return (strpos($text, 'Mechanin') !== false)
            ? 'Mechaninė'
            : 'Automatinė';
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
    public function detectExternalId($text): string
    {
        $pattern = '/lt\/vehicle\/(\d*)\//';
        preg_match($pattern, $text, $matches);
        return $matches[1];
    }
}