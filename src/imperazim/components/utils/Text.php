<?php

declare(strict_types = 1);

namespace imperazim\components\utils;

/**
* Class Text
* @package imperazim\components\utils
*/
final class Text {

  /**
  * Insert new lines every $maxLength characters, ensuring words are not split in the middle.
  * @param string $text The input text.
  * @param int $maxLength The maximum length of each line.
  * @return string The formatted text with line breaks.
  */
  public static function insertLineBreaks(string $text, int $maxLength = 36): string {
    try {
      $words = explode(' ', $text);
      $lines = [];
      $currentLine = '';

      foreach ($words as $word) {
        if (strlen($currentLine) + strlen($word) + 1 <= $maxLength) {
          $currentLine .= ($currentLine === '' ? '' : ' ') . $word;
        } else {
          $lines[] = $currentLine;
          $currentLine = $word;
        }
      }

      if ($currentLine !== '') {
        $lines[] = $currentLine;
      }
      return implode("\n", $lines);
    } catch (\Exception $e) {
      new \crashdump($e);
      return $text;
    }
  }

  /**
  * Remove all HTML tags from the text.
  * @param string $text The input text.
  * @return string The plain text.
  */
  public static function stripHtmlTags(string $text): string {
    return strip_tags($text);
  }

  /**
  * Remove extra spaces from the text.
  * @param string $text The input text.
  * @return string The cleaned text.
  */
  public static function removeExtraSpaces(string $text): string {
    return preg_replace('/\s+/', ' ', trim($text));
  }

  /**
  * Convert the text to uppercase.
  * @param string $text The input text.
  * @return string The uppercased text.
  */
  public static function toUpperCase(string $text): string {
    return strtoupper($text);
  }

  /**
  * Convert the text to lowercase.
  * @param string $text The input text.
  * @return string The lowercased text.
  */
  public static function toLowerCase(string $text): string {
    return strtolower($text);
  }

  /**
  * Truncate the text to a specified length, adding ellipsis if needed.
  * @param string $text The input text.
  * @param int $maxLength The maximum length of the text.
  * @return string The truncated text.
  */
  public static function truncate(string $text, int $maxLength = 100): string {
    return (strlen($text) > $maxLength) ? substr($text, 0, $maxLength - 3) . '...' : $text;
  }

  /**
  * Remove special characters from the text.
  * @param string $text The input text.
  * @return string The cleaned text.
  */
  public static function removeSpecialChars(string $text): string {
    return preg_replace('/[^a-zA-Z0-9\s]/', '', $text);
  }
}