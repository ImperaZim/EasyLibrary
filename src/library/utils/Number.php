<?php

declare(strict_types = 1);

namespace library\utils;

/**
* Class Number
* @package library\utils
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
    if ($number >= 1000000) {
      return number_format($number / 1000000, 1) . 'M';
    } elseif ($number >= 1000) {
      return number_format($number / 1000, 1) . 'K';
    } else {
      return (string) $number;
    }
  }
}