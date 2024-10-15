<?php

namespace imperazim\components\utils;

use Library;
use pocketmine\scheduler\ClosureTask;

/**
* Final class FunctionUtils 
* @package imperazim\components\utils
*/
final class FunctionUtils {

  /**
  * Schedules a delayed function to be executed after a given number of ticks.
  * @param callable $function The function to be executed after the delay.
  * @param int $timeInTicks The delay time in ticks (20 ticks = 1 second).
  * @return void
  */
  public static function sendDelayedFunction(callable $function, int $timeInTicks): void {
    $plugin = Library::getInstance();
    if ($plugin !== null && $plugin->isEnabled()) {
      $plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($function): void {
        $function();
      }), $timeInTicks);
    }
  }
}