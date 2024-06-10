<?php

declare(strict_types = 1);

namespace internal\dialogue\interaction;

use Closure;
use pocketmine\player\Player;
use internal\dialogue\elements\DialogueButton;

/**
* Class DialogueButtonResponse
* @package internal\dialogue\interaction
*/
final class DialogueButtonResponse {

  /**
  * ButtonResponse constructor.
  * @param Closure(Player, DialogueButton): void $callback
  */
  public function __construct(private Closure $callback) {}

  /**
  * Execute the response callback.
  * @param Player $player
  * @param DialogueButton $button
  */
  public function runAt(Player $player, DialogueButton $button): void {
    ($this->callback)($player, $button);
  }
  
}