<?php

declare(strict_types = 1);

namespace internal\libform\elements;

use internal\libform\traits\IdentifiableElement;

/**
* Class Content
* @param internal\libform\elements
*/
final class Content implements \JsonSerializable {
  use IdentifiableElement;

  /**
  * Content constructor.
  * @param string $text The text displayed on the content.
  */
  public function __construct(private string $text) {}

  /**
  * Defines the text of the content.
  * @param string $text The new text for the content.
  * @return self Returns the current instance for method chaining.
  */
  public function setText(string $text): self {
    $this->text = $text;
    return $this;
  }

  /**
  * Gets the text of the content.
  * @return string The text of the content.
  */
  public function getText(): string {
    return is_array($this->text) ? implode("§r\n", $this->text) : $this->text;
  }

  /**
  * Serializes the content to an array for JSON encoding.
  * @return array<string, mixed> The serialized data of the content.
  */
  public function jsonSerialize(): array {
    $data = [
      "text" => $this->getText()
    ];
    return $data;
  }
}