<?php

declare(strict_types = 1);

namespace imperazim\components\trigger;

/**
* Class GlobalTrigger
* Represents a trigger that checks a condition and executes an action globally.
*
* @package imperazim\components\trigger
*/
final class GlobalTrigger extends Trigger {

  /**
  * GlobalTrigger constructor.
  *
  * @param Closure $condition A Closure that returns a boolean value. If true, the action is executed.
  * @param Closure $action A Closure that defines the action to be executed when the condition is met.
  */
  public function __construct(\Closure $condition, \Closure $action) {
    parent::__construct($condition, $action, TriggerTypes::GLOBAL);
  }
}