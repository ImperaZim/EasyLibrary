<?php

declare(strict_types = 1);

namespace imperazim\vendor\dialogue\elements;

use Closure;
use pocketmine\player\Player;
use imperazim\vendor\dialogue\interaction\DialogueButtonResponse;

/**
* Class DialogueButton
* @package imperazim\vendor\dialogue\elements
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
  public function __construct(
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
  * Sets the name of the button.
  * @param string|null $name The name of the button.
  * @return self Returns the current instance for method chaining.
  */
  public function setName(?string $name): self {
    $this->name = $name;
    return $this;
  }

  /**
  * Gets the text displayed on the button.
  * @return string The text displayed on the button.
  */
  public function getText() : string {
    return $this->text;
  }

  /**
  * Sets the text displayed on the button.
  * @param string|null $text The text displayed on the button.
  * @return self Returns the current instance for method chaining.
  */
  public function setText(?string $text): self {
    $this->text = $text;
    return $this;
  }

  /**
  * Gets the optional data associated with the button.
  * @return string|null The optional data associated with the button.
  */
  public function getData() : ?string {
    return $this->data;
  }

  /**
  * Sets the optional data associated with the button.
  * @param string|null $data The optional data associated with the button.
  * @return self Returns the current instance for method chaining.
  */
  public function setData(?string $data): self {
    $this->data = $data;
    return $this;
  }

  /**
  * Gets the mode of the button.
  * @return int The mode of the button.
  */
  public function getMode() : int {
    return $this->mode;
  }

  /**
  * Sets the mode of the button.
  * @param int|null $mode The mode of the button.
  * @return self Returns the current instance for method chaining.
  */
  public function setMode(?int $mode): self {
    $this->mode = $mode;
    return $this;
  }

  /**
  * Gets the type of the button.
  * @return int The type of the button.
  */
  public function getType() : int {
    return $this->type;
  }

  /**
  * Sets the type of the button.
  * @param int|null $type The type of the button.
  * @return self Returns the current instance for method chaining.
  */
  public function setType(?int $type): self {
    $this->type = $type;
    return $this;
  }

  /**
  * Gets the response function of the button.
  * @return DialogueButtonResponse|null The response function of the button.
  */
  public function getResponse(): ?DialogueButtonResponse {
    return $this->onclick;
  }

  /**
  * Sets the response function of the button.
  * @param DialogueButtonResponse|null $response The response function of the button.
  * @return self Returns the current instance for method chaining.
  */
  public function setResponse(?DialogueButtonResponse $response): self {
    $this->onclick = $response;
    return $this;
  }
}