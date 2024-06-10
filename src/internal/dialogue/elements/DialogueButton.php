<?php

declare(strict_types = 1);

namespace internal\dialogue\elements;

use Closure;
use pocketmine\player\Player;
use internal\dialogue\interaction\DialogueButtonResponse;

/**
* Class DialogueButton
* @package internal\dialogue\elements
*/
final class DialogueButton {

  /**
  * DialogueButton constructor.
  * @param string|null $name The name of the button.
  * @param string|null $text The text displayed on the button.
  * @param string|null $data Optional data associated with the button.
  * @param int|null $mode The mode of the button.
  * @param int|null $type The type of the button.
  * @param DialogueButtonResponse|null $onclick The function to execute when the button is clicked.
  */
  private function __construct(
    private ?string $name = 'Default Name',
    private ?string $text = 'Default Text',
    private ?string $data = '',
    private ?int $mode = 0,
    private ?int $type = 1,
    private ?DialogueButtonResponse $onclick = null
  ) {}

  /**
  * Gets the name of the button.
  * @return string The name of the button.
  */
  public function getName() : string {
    return $this->name;
  }

  /**
  * Gets the text displayed on the button.
  * @return string The text displayed on the button.
  */
  public function getText() : string {
    return $this->text;
  }

  /**
  * Gets the optional data associated with the button.
  * @return string|null The optional data associated with the button.
  */
  public function getData() : ?string {
    return $this->data;
  }

  /**
  * Gets the mode of the button.
  * @return int The mode of the button.
  */
  public function getMode() : int {
    return $this->mode;
  }

  /**
  * Gets the type of the button.
  * @return int The type of the button.
  */
  public function getType() : int {
    return $this->type;
  }

  /**
  * Gets the response function of the button.
  * @return DialogueButtonResponse|null The response function of the button.
  */
  public function getResponse(): ?DialogueButtonResponse {
    return $this->onclick;
  }

  /**
  * Executes the function associated with the button when clicked.
  * @param Player $player The player who clicked the button.
  */
  public function onClick(Player $player) : void {
    $buttonResponse = $this->getResponse();
    if ($buttonResponse !== null) {
      $buttonResponse->runAt($player, $this);
    }
  }
}