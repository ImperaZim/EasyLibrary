<?php

declare(strict_types = 1);

namespace libraries\form\menu;

use JetBrains\PhpStorm\Immutable;
use libraries\form\traits\IdentifiableElement;

#[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
class Button implements \JsonSerializable {
  use IdentifiableElement;

  public function __construct(
    public string|array $text, 
    public ?Image $image = null, 
    public ?string $value = null
  ) {}

  public function getValue(): ?string {
    return $this->value;
  }

  public function setValue(string $value): self {
    $this->value = $value;
    return $this;
  }

  /** @phpstan-return array<string, mixed> */
  public function jsonSerialize(): array {
    $text = is_array($this->text)
    ? implode("§r\n", $this->text)
    : $this->text;
    $ret = ["text" => $text];
    if ($this->image !== null) {
      $ret["image"] = $this->image;
    }

    return $ret;
  }
}