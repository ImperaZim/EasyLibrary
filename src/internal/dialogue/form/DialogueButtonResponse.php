<?php

declare(strict_types = 1);

namespace internal\dialogue\form;

use Closure;
use pocketmine\player\Player;

/**
* Class DialogueButtonResponse
* @package internal\dialogue\form
*/
final class DialogueButtonResponse {

  /**
  * DialogueButtonResponse constructor.
  * @param Closure(Player, DialogueButton): void $callback
  */
  public function __construct(private Closure $callback) {}

  /**
  * Execute the response callback.
  * @param Player $player
  * @param Button $button
  */
  public function runAt(Player $player, DialogueButton $button): void {
    ($this->callback)($player, $button);
  }
  
}