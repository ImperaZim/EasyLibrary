<?php

declare(strict_types = 1);

namespace imperazim\components\utils;

/**
* Class Number
* @package imperazim\components\utils
*/
final class Number {

  /**
  * Convert a number to a formatted money string.
  * Example: 10000 => 10.000
  *
  * @param int|float $number
  * @param int $decimals Number of decimal places
  * @param string $decimalSeparator Decimal separator
  * @param string $thousandSeparator Thousand separator
  * @return string
  */
  public static function formatAsCurrency($number, int $decimals = 0, string $decimalSeparator = ',', string $thousandSeparator = '.'): string {
    return number_format($number, $decimals, $decimalSeparator, $thousandSeparator);
  }

  /**
  * Convert a number to a statistical shorthand string.
  * Example: 10000 => 10K
  *
  * @param int|float $number
  * @return string
  */
  public static function formatAsStatistic($number): string {
    $suffixes = [
      1e30 => 'N',
      // Nonillion
      1e27 => 'O',
      // Octillion
      1e24 => 'S',
      // Septillion
      1e21 => 's',
      // Sextillion
      1e18 => 'Qn',
      // Quintillion
      1e15 => 'Qa',
      // Quadrillion
      1e12 => 'T',
      // Trillion
      1e9 => 'B',
      // Billion
      1e6 => 'M',
      // Million
      1e3 => 'K',
      // Thousand
    ];

    foreach ($suffixes as $value => $suffix) {
      if ($number >= $value) {
        return number_format($number / $value, 1) . $suffix;
      }
    }

    return (string) $number;
  }

  /**
  * Formats the given ID as a string with leading zeros if it's numeric.
  * @param int $length
  * @param string|int $number
  * @return string
  */
  public static function format(int $length, string|int $number): string {
    return is_numeric($number) ? sprintf('%0' . $length . 'd', $number) : $number;
  }

  /**
  * Convert a number to a formatted percentage string.
  * Example: 0.85 => 85%
  *
  * @param float $number The input number.
  * @param int $decimals Number of decimal places.
  * @return string The formatted percentage string.
  */
  public static function formatAsPercentage(float $number, int $decimals = 2): string {
    return number_format($number * 100, $decimals) . '%';
  }

  /**
  * Round a number to a specified number of decimal places.
  *
  * @param float $number The input number.
  * @param int $decimals Number of decimal places.
  * @return float The rounded number.
  */
  public static function roundToDecimals(float $number, int $decimals = 2): float {
    return round($number, $decimals);
  }

  /**
  * Get the maximum number from an array of numbers.
  *
  * @param array $numbers The array of numbers.
  * @return int|float|null The maximum number or null if the array is empty.
  */
  public static function max(array $numbers) {
    return !empty($numbers) ? max($numbers) : null;
  }

  /**
  * Get the minimum number from an array of numbers.
  *
  * @param array $numbers The array of numbers.
  * @return int|float|null The minimum number or null if the array is empty.
  */
  public static function min(array $numbers) {
    return !empty($numbers) ? min($numbers) : null;
  }

  /**
  * Generate a random number within a specified range.
  *
  * @param int $min The minimum value.
  * @param int $max The maximum value.
  * @return int The random number.
  */
  public static function random(int $min = 0, int $max = PHP_INT_MAX): int {
    return rand($min, $max);
  }
}