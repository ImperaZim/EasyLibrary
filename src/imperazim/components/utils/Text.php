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

}