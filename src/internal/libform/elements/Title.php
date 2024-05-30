<?php

declare(strict_types = 1);

namespace internal\libform\elements;

use internal\libform\traits\IdentifiableElement;

/**
* Class Title
* @param internal\libform\elements
*/
final class Title implements \JsonSerializable {
  use IdentifiableElement;

  /**
  * Title constructor.
  * @param string $text The text displayed on the title.
  */
  public function __construct(private string $text) {}

  /**
  * Defines the text of the title.
  * @param string $text The new text for the title.
  * @return self Returns the current instance for method chaining.
  */
  public function setText(string $text): self {
    $this->text = $text;
    return $this;
  }

  /**
  * Gets the text of the title.
  * @return string The text of the title.
  */
  public function getText(): string {
    return is_array($this->text) ? implode("§r\n", $this->text) : $this->text;
  }

  /**
  * Serializes the title to an array for JSON encoding.
  * @return array<string, mixed> The serialized data of the title.
  */
  public function jsonSerialize(): array {
    $data = [
      "text" => $this->getText()
    ];
    return $data;
  }
}