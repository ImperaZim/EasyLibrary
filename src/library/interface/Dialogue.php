<?php

declare(strict_types = 1);

namespace library\interface;

use pocketmine\player\Player;
use internal\dialogue\Dialogue as IDialogue;

/**
* Class Dialogue
* @package library\interface
*/
abstract class Dialogue {

  /** @var IDialogue|null */
  private ?IDialogue $dialogue = null;

  /**
  * Dialogue constructor.
  * @param Player $player
  * @param array|null $data
  */
  public function __construct(
    private Player $player,
    private ?array $data = []
  ) {
    $this->dialogue = $this->structure();
    $this->send();
  }

  /**
  * Send the dialogue to the player.
  */
  public function send(): void {
    try {
      $dialogue = $this->dialogue;
      if ($dialogue instanceof IDialogue) {
        $dialogue->sendTo($this->player);
      }
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Get the associated player.
  * @return Player
  */
  public function getPlayer() : Player {
    return $this->player;
  }

  /**
  * Construct and set up the dialogue.
  */
  protected abstract function structure(): IDialogue;

  /**
  * Get processed data by key.
  * @param string|null $key
  * @return mixed|null
  */
  public function getProcessedData(?string $key = 'null') : mixed {
    try {
      if ($key != 'null') {
        $value = isset($this->data[$key]) ? $this->data[$key] : null;
      } else {
        $value = $this->data;
      }
      return $value;
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
    return null;
  }

}