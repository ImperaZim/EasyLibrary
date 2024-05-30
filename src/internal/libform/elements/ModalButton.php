<?php

declare(strict_types = 1);

namespace internal\libform\elements;

use internal\libform\interaction\ModalButtonResponse;
use internal\libform\traits\IdentifiableElement;

/**
* Class ModalButton
* @param internal\libform\elements
*/
final class ModalButton implements \JsonSerializable {
  use IdentifiableElement;

  /**
  * ModalButton constructor.
  * @param string $text The text displayed on the button.
  * @param ModalButtonResponse|null $onclick The response function to be executed when the button is pressed.
  */
  public function __construct(
    private string $text,
    private ?ModalButtonResponse $onclick = null,
  ) {}

  /**
  * Defines the text of the button.
  * @param string $text The new text for the button.
  * @return self Returns the current instance for method chaining.
  */
  public function setText(string $text): self {
    $this->text = $text;
    return $this;
  }

  /**
  * Gets the text of the button.
  * @return string The text of the button.
  */
  public function getText(): string {
    return $this->text;
  }

  /**
  * Defines the response function of the button.
  * @param ModalButtonResponse $onclick The new response function for the button.
  * @return self Returns the current instance for method chaining.
  */
  public function setResponse(ModalButtonResponse $onclick): self {
    $this->onclick = $onclick;
    return $this;
  }

  /**
  * Gets the response function of the button.
  * @return ModalButtonResponse|null The response function of the button.
  */
  public function getResponse(): ?ModalButtonResponse {
    return $this->onclick;
  }

  /**
  * Serializes the button to an array for JSON encoding.
  * @return array<string, mixed> The serialized data of the button.
  */
  public function jsonSerialize(): array {
    $data = [
      "text" => $this->getText()
    ];
    return $data;
  }
}