<?php

declare(strict_types = 1);

namespace imperazim\vendor\libform\interaction;

use Closure;
use pocketmine\player\Player;
use imperazim\vendor\libform\elements\Button;

/**
* Class ButtonResponse
* @package imperazim\vendor\libform\interaction
*/
final class ButtonResponse {

  /**
  * ButtonResponse constructor.
  * @param Closure(Player, Button): void $callback
  */
  public function __construct(private Closure $callback) {}

  /**
  * Execute the response callback.
  * @param Player $player
  * @param Button $button
  */
  public function runAt(Player $player, Button $button): void {
    ($this->callback)($player, $button);
  }
  
}