<?php

declare(strict_types = 1);

namespace imperazim\components\trigger;

use pocketmine\player\Player;

/**
* Class PerPlayerTrigger
* Represents a trigger that checks a condition and executes an action for a specific player.
*
* @package imperazim\components\trigger
*/
final class PerPlayerTrigger extends Trigger {

  /**
  * PerPlayerTrigger constructor.
  *
  * @param callable $condition A callable that takes a Player and returns a boolean value. If true, the action is executed.
  * @param callable $action A callable that takes a Player and defines the action to be executed when the condition is met.
  */
  public function __construct(callable $condition, callable $action) {
    parent::__construct($condition, $action, TriggerTypes::PER_PLAYER);
  }

  /**
  * Checks if the condition is met for the specific player, and if so, executes the action.
  *
  * @param Player $player The player associated with this trigger.
  */
  public function checkCondition(Player $player): void {
    if (($this->condition)($player)) {
      ($this->action)($player);
    }
  }
}