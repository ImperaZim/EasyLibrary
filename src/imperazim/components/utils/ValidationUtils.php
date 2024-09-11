<?php

declare(strict_types = 1);

namespace imperazim\components\utils;

/**
* Class ValidationUtils
* @package imperazim\components\utils
*/
final class ValidationUtils {
  
  /**
  * Validates an email address.
  * @param string $email The email address to validate.
  * @return bool True if the email address is valid, false otherwise.
  */
  public static function isEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
  }

  /**
  * Validates a phone number.
  * @param string $phone The phone number to validate.
  * @return bool True if the phone number is valid, false otherwise.
  */
  public static function isPhoneNumber(string $phone): bool {
    return preg_match('/^\+?[1-999]\d{1,14}$/', $phone) === 1;
  }

  /**
  * Validates a URL.
  * @param string $url The URL to validate.
  * @return bool True if the URL is valid, false otherwise.
  */
  public static function isUrl(string $url): bool {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
  }

  /**
  * Checks if a value is an integer.
  * @param string $value The value to check.
  * @return bool True if the value is an integer, false otherwise.
  */
  public static function isInteger(string $value): bool {
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
  }
}