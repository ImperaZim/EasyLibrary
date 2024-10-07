<?php

declare(strict_types = 1);

namespace imperazim\vendor\libform\elements;

use imperazim\vendor\libform\interaction\ButtonResponse;
use imperazim\vendor\libform\traits\IdentifiableElement;

/**
* Class Button
* @param imperazim\vendor\libform\elements
*/
final class Button implements \JsonSerializable {
  use IdentifiableElement;

  /**
  * Button constructor.
  * @param string|array $text The text displayed on the button.
  * @param Image|null $image The image associated with the button.
  * @param string|null $identifier The identifier associated with the button.
  * @param ButtonResponse|null $response The response function to be executed when the button is pressed.
  */
  public function __construct(
    private string|array $text,
    private ?Image $image = null,
    private ?string $identifier = null,
    private ?ButtonResponse $response = null,
    private ?bool $reopen = false
  ) {}

  /**
  * Defines the text of the button.
  * @param string|array $text The new text for the button.
  * @return self Returns the current instance for method chaining.
  */
  public function setText(string|array $text): self {
    $this->text = $text;
    return $this;
  }

  /**
  * Gets the text of the button.
  * @return string The text of the button.
  */
  public function getText(): string {
    return is_array($this->text) ? implode("§r\n", $this->text) : $this->text;
  }

  /**
  * Defines the image of the button.
  * @param Image $image The new image for the button.
  * @return self Returns the current instance for method chaining.
  */
  public function setImage(Image $image): self {
    $this->image = $image;
    return $this;
  }

  /**
  * Gets the image of the button.
  * @return Image|null The image of the button.
  */
  public function getImage(): ?Image {
    return $this->image;
  }

  /**
  * Defines the identifier of the button.
  * @param string $identifier The new identifier for the button.
  * @return self Returns the current instance for method chaining.
  */
  public function setIdentifier(string $identifier): self {
    $this->identifier = $identifier;
    return $this;
  }

  /**
  * Gets the identifier of the button.
  * @return string|null The identifier of the button.
  */
  public function getIdentifier(): ?string {
    return $this->identifier;
  }

  /**
  * Defines the response function of the button.
  * @param ButtonResponse $response The new response function for the button.
  * @return self Returns the current instance for method chaining.
  */
  public function setResponse(ButtonResponse $response): self {
    $this->response = $response;
    return $this;
  }

  /**
  * Gets the response function of the button.
  * @return ButtonResponse|null The response function of the button.
  */
  public function getResponse(): ?ButtonResponse {
    return $this->response;
  }

  /**
  * Defines whether to reopen.
  * @param bool $reopen The new value for should reopen.
  * @return self Returns result whether to reopen.
  */
  public function setShouldReopen(bool $reopen): self {
    $this->reopen = $reopen;
    return $this;
  }

  /**
  * Gets result whether to reopen
  * @return bool Gets result whether to reopen
  */
  public function getShouldReopen(): bool {
    return $this->reopen;
  }

  /**
  * Serializes the button to an array for JSON encoding.
  * @return array<string, mixed> The serialized data of the button.
  */
  public function jsonSerialize(): array {
    $data = [
      "text" => $this->getText()
    ];
    if ($this->image !== null) {
      $data["image"] = $this->image;
    }
    return $data;
  }
}