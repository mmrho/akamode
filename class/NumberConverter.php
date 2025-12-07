<?php
declare(strict_types=1);

/**
 * Class NumberConverter
 * A utility class for handling number conversions between English, Persian, and Arabic scripts.
 * No namespace is used for easier global access within the theme.
 */
class NumberConverter
{
    /**
     * Converts English digits to Persian digits.
     * Optionally converts decimal points (.) to Persian decimal separator (٫).
     *
     * @param string|int|float $input The input string or number.
     * @param bool $format_decimal Whether to convert '.' to Persian decimal separator '٫'.
     * @return string The converted string with Persian digits.
     */
    public static function toPersian(string|int|float $input, bool $format_decimal = false): string
    {
        $string = (string) $input;

        if (empty($string)) {
            return '';
        }

        $en_digits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $fa_digits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

        $converted = str_replace($en_digits, $fa_digits, $string);

        // Convert decimal point if requested
        if ($format_decimal) {
            $converted = str_replace('.', '٫', $converted);
        }

        return $converted;
    }

    /**
     * Converts Persian and Arabic digits to English digits.
     * Useful for sanitizing user input before saving to the database or for 'tel:' links.
     *
     * @param string|int|float $input The input string containing Persian/Arabic digits.
     * @return string The converted string with English digits.
     */
    public static function toEnglish(string|int|float $input): string
    {
        $string = (string) $input;

        if (empty($string)) {
            return '';
        }

        // Arrays include both Persian and Arabic numerals to ensure full compatibility
        $persian_arabic_digits = [
            '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', // Persian
            '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', // Arabic
            '٫' // Persian Decimal Separator
        ];

        $english_digits = [
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', // Mapped to Persian
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', // Mapped to Arabic
            '.' // Mapped to Decimal Point
        ];

        return str_replace($persian_arabic_digits, $english_digits, $string);
    }

    /**
     * Helper method to format price specifically.
     * Adds commas to the number (1000 -> 1,000) and then converts to Persian.
     * * @param int|float $price
     * @return string
     */
    public static function formatPrice($price): string
    {
        return self::toPersian(number_format((float)$price));
    }
}

// ------------------------------------------------------------------
// Global Helper Functions 
// These functions allow you to use the class without typing "NumberConverter::"
// ------------------------------------------------------------------

if (!function_exists('tr_num_fa')) {
    /**
     * Global helper to convert to Persian numbers.
     * Usage: echo tr_num_fa('123');
     */
    function tr_num_fa($input) {
        return NumberConverter::toPersian($input);
    }
}

if (!function_exists('tr_num_en')) {
    /**
     * Global helper to convert to English numbers.
     * Usage: echo tr_num_en('۱۲۳');
     */
    function tr_num_en($input) {
        return NumberConverter::toEnglish($input);
    }
}