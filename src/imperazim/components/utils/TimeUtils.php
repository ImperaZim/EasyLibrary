<?php

declare(strict_types = 1);

namespace imperazim\components\utils;

/**
* Class TimeUtils
* @package imperazim\components\utils
*/
final class TimeUtils {

  /**
  * Checks if the current time matches the given time in "HH:MM:SS" format.
  * Supports wildcard "#" for any hour, minute, or second.
  * @param string $time
  * @return bool
  */
  public static function IsTime(string $time): bool {
    $currentTime = date('H:i:s');
    $timePattern = preg_quote($time, '/');
    $timePattern = str_replace('#', '\d{2}', $timePattern);
    return (bool)preg_match("/^{$timePattern}$/", $currentTime);
  }


  /**
  * Checks if the current date matches the given date in "DD/MM" format.
  * Supports wildcard "#" for any day or month.
  * @param string $date
  * @return bool
  */
  public static function IsDate(string $date): bool {
    $currentDate = date('d/m');
    $datePattern = preg_quote($date, '/');
    $datePattern = str_replace('#', '\d{2}', $datePattern);
    return (bool)preg_match("/^{$datePattern}$/", $currentDate);
  }


  /**
  * Returns the current time in the specified format.
  * Default is "H:i:s".
  * @param string $format
  * @return string
  */
  public static function GetCurrentTime(string $format = 'H:i:s'): string {
    return date($format);
  }

  /**
  * Returns the current date in the specified format.
  * Default is "d/m/Y".
  * @param string $format
  * @return string
  */
  public static function GetCurrentDate(string $format = 'd/m/Y'): string {
    return date($format);
  }

  /**
  * Checks if today is a weekend (Saturday or Sunday).
  * @return bool
  */
  public static function IsWeekend(): bool {
    $dayOfWeek = date('N');
    return $dayOfWeek >= 6;
  }

  /**
  * Checks if the given year is a leap year.
  * If no year is provided, checks the current year.
  * @param int|null $year
  * @return bool
  */
  public static function IsLeapYear(int $year = null): bool {
    $year = $year ?? (int)date('Y');
    return ($year % 4 === 0 && $year % 100 !== 0) || ($year % 400 === 0);
  }

  /**
  * Adds a specific amount of time (hours, minutes, or seconds) to a given time.
  * @param string $time
  * @param int $hours
  * @param int $minutes
  * @param int $seconds
  * @return string
  */
  public static function AddTime(string $time, int $hours = 0, int $minutes = 0, int $seconds = 0): string {
    $dateTime = new \DateTime($time);
    $dateTime->modify("+{$hours} hours +{$minutes} minutes +{$seconds} seconds");
    return $dateTime->format('H:i:s');
  }

  /**
  * Calculates the difference between two times in "H:i:s" format.
  * @param string $time1
  * @param string $time2
  * @return string
  */
  public static function DiffBetweenTimes(string $time1, string $time2): string {
    $start = new \DateTime($time1);
    $end = new \DateTime($time2);
    $interval = $start->diff($end);
    return $interval->format('%H:%I:%S');
  }

  /**
  * Checks if the given time has already passed today.
  * @param string $time
  * @return bool
  */
  public static function IsPastTime(string $time): bool {
    $currentTime = date('H:i:s');
    return strtotime($time) < strtotime($currentTime);
  }

  /**
  * Calculates the number of days between two dates in "d/m/Y" format.
  * @param string $date1
  * @param string $date2
  * @return int
  */
  public static function DaysBetweenDates(string $startDate, string $endDate): int {
    $start = \DateTime::createFromFormat('d/m/Y', $startDate);
    $end = \DateTime::createFromFormat('d/m/Y', $endDate);

    if (!$start || !$end) {
      return -1;
    }

    $interval = $start->diff($end);
    return $interval->days;
  }

  /**
  * Returns the start of the current day as "00:00:00".
  * @return string
  */
  public static function GetStartOfDay(): string {
    return '00:00:00';
  }

  /**
  * Returns the end of the current day as "23:59:59".
  * @return string
  */
  public static function GetEndOfDay(): string {
    return '23:59:59';
  }
}