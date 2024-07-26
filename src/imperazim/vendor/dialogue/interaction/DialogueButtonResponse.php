<?php

declare(strict_types = 1);

namespace imperazim\vendor\dialogue\interaction;

use Closure;
use pocketmine\player\Player;
use imperazim\vendor\dialogue\Dialogue;
use imperazim\vendor\dialogue\elements\DialogueButton;

/**
* Class DialogueButtonResponse
* @package imperazim\vendor\dialogue\interaction
*/
final class DialogueButtonResponse {

  /**
  * ButtonResponse constructor.
  * @param Closure(Player, DialogueButton, Dialogue): void $callback
  */
  public function __construct(private Closure $callback) {}

  /**
  * Execute the response callback.
  * @param Player $player
  * @param DialogueButton $button
  */
  public function runAt(Player $player, DialogueButton $button, Dialogue &$dialogue): void {
    ($this->callback)($player, $button, $dialogue);
  }
  
}