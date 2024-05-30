<?php

declare(strict_types = 1);

namespace internal\libform\interaction;

use Closure;
use pocketmine\player\Player;
use internal\libform\elements\Button;

/**
* Class ModalButtonResponse
* @package internal\libform\interaction
*/
final class ModalButtonResponse {

  /**
  * ModalButtonResponse constructor.
  * @param Closure(Player): void $callback
  */
  public function __construct(private Closure $callback) {}

  /**
  * Execute the response callback.
  * @param Player $player
  */
  public function runAt(Player $player): void {
    ($this->callback)($player);
  }
  
}