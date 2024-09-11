<?php

declare(strict_types = 1);

namespace imperazim\components\trigger;

/**
* Class Trigger
* Represents a task-based trigger that checks a condition and executes an action when the condition is met.
*
* @package imperazim\components\trigger
*/
class Trigger {

  /**
  * @var \Closure The condition that needs to be satisfied for the trigger to execute the action.
  */
  protected \Closure $condition;

  /**
  * @var \Closure The action to be executed when the condition is met.
  */
  protected \Closure $action;

  /**
  * @var int The type of trigger.
  */
  private int $triggerType;

  /**
  * Trigger constructor.
  *
  * @param \Closure $condition A Closure that returns a boolean value. If true, the action is executed.
  * @param \Closure $action A Closure that defines the action to be executed when the condition is met.
  * @param int $triggerType An integer representing the type of the trigger, using constants from TriggerTypes.
  */
  public function __construct(\Closure $condition, \Closure $action, int $triggerType) {
    $this->condition = $condition;
    $this->action = $action;
    $this->triggerType = $triggerType;
  }

  /**
  * Returns the type of the trigger.
  *
  * @return int The type of the trigger.
  */
  public function getTriggerType(): int {
    return $this->triggerType;
  }
}