<?php

namespace App\Helpers;

class Shorty
{
    /**
     * Default characters to use for shortening.
     *
     * @var string
     */
    private string $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    /**
     * Salt for id encoding.
     *
     * @var string
     */
    private string $salt;

    /**
     * Length of number padding.
     *
     * @var int
     */
    private int $padding = 1;

    public function __construct(string $chars, string $salt, int $padding)
    {
        $this->chars = $chars;
        $this->salt = $salt;
        $this->padding = $padding;
    }

    /**
     * Converts an id to an encoded string.
     *
     * @param int $number
     * @return string
     */
    public function encode(int $number): string
    {
        $hash = 0;

        if ($this->padding > 0 && !empty($this->salt)) {
            $hash = self::getSeed($number, $this->salt, $this->padding);
            $number = (int)($hash . $number);
        }

        return self::numberToAlpha($number, $this->chars);
    }

    /**
     * Converts an encoded string into a number.
     *
     * @param string $string
     * @return int
     */
    public function decode(string $string): int
    {
        $number = self::alphaToNumber($string, $this->chars);

        return (!empty($this->salt)) ? substr($number, $this->padding) : $number;
    }

    /**
     * Gets a number for padding base on a salt
     *
     * @param int $number Number to pad
     * @param string $salt Salt
     * @param int $padding Padding length
     * @return string Number for padding
     */
    public static function getSeed(int $number, string $salt, int $padding): string
    {
        $hash = md5($number . $salt);
        $dec = hexdec(substr($hash, 0, $padding));
        $num = $dec % pow(10, $padding);
        if ($num == 0) $num = 1;
        return str_pad($num, $padding, '0');
    }

    /**
     * Converts a number to an alphanumeric string
     *
     * @param int $number
     * @param string $characters
     * @return string
     */
    public static function numberToAlpha(int $number, string $characters): string
    {
        $length = strlen($characters);
        $math = $number % $length;

        if ($number - $math == 0) return substr($characters, $number, 1);

        $alpha = '';

        while ($math > 0 || $number > 0) {
            $alpha = substr($characters, $math, 1) . $alpha;
            $number = ($number - $math) / $length;
            $math = $number % $length;
        }

        return $alpha;
    }

    /**
     * Converts an alphanumeric string to a number
     *
     * @param string $alpha
     * @param string $characters
     * @return int
     */
    public static function alphaToNumber(string $alpha, string $characters): int
    {
        $b = strlen($characters);
        $l = strlen($alpha);

        for ($number = 0, $i = 0; $i < $l; $i++) {
            $number += strpos($characters, substr($alpha, $i, 1)) * pow($b, $l - $i - 1);
        }

        return $number;
    }
}