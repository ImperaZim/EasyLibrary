<?php

declare(strict_types = 1);

namespace imperazim\components\trigger;

/**
* Class Trigger
* Represents a task-based trigger that checks a condition and executes an action when the condition is met.
*
* @package imperazim\components\trigger
*/
final class Trigger {

  /**
  * @var callable The condition that needs to be satisfied for the trigger to execute the action.
  */
  private callable $condition;

  /**
  * @var callable The action to be executed when the condition is met.
  */
  private callable $action;

  /**
  * @var int The type of trigger.
  */
  private int $triggerType;

  /**
  * Trigger constructor.
  *
  * @param callable $condition A callable that returns a boolean value. If true, the action is executed.
  * @param callable $action A callable that defines the action to be executed when the condition is met.
  * @param int $triggerType An integer representing the type of the trigger, using constants from TriggerTypes.
  */
  public function __construct(callable $condition, callable $action, int $triggerType) {
    $this->condition = $condition;
    $this->action = $action;
    $this->triggerType = $triggerType;
  }

  /**
  * Checks if the condition is met, and if so, executes the action.
  */
  public function checkCondition(): void {
    if (($this->condition)()) {
      ($this->action)();
    }
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