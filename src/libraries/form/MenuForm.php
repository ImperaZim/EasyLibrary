<?php

declare(strict_types = 1);

namespace libraries\form;

use Closure;
use pocketmine\player\Player;
use pocketmine\form\FormValidationException;

use libraries\form\menu\Image;
use libraries\form\menu\Button;

use function is_int;
use function gettype;
use function is_null;

class MenuForm extends BaseForm {

  private array $buttons = [];
  /**
  * @param Button[] $buttons
  * @phpstan-param (Closure(Player, Button): mixed)|null $onSubmit
  * @phpstan-param (Closure(Player): mixed)|null $onClose
  */
  public function __construct(
    string $title,
    protected string $content = '',
    array $buttons = [],
    private ?Closure $onSubmit = null,
    private ?Closure $onClose = null,
  ) {
    $this->buttons = (array) $buttons;
    parent::__construct($title);
  }

  /** @phpstan-param list<string> $options */
  public static function withOptions(
    string $title,
    string $content = '',
    array $options = [],
    ?Closure $onSubmit = null,
    ?Closure $onClose = null,
  ): self {
    /** @var Button[] $buttons */
    $buttons = [];
    foreach ($options as $option) {
      $buttons[] = new Button($option);
    }
    return new self($title, $content, $buttons, $onSubmit, $onClose);
  }

  /**
  * @phpstan-param Closure(Player, Button): void $closure
  */
  public function onSubmit(Closure $closure): void {
    $this->onSubmit = $closure;
  }

  /**
  * @phpstan-param Closure(Player): void $closure
  */
  public function onClose(Closure $closure): void {
    $this->onClose = $closure;
  }

  public function getContent(): string {
    return $this->content;
  }

  public function setContent(string $content): void {
    $this->content = $content;
  }

  public function addButton(string $text, ?Image $image = null, ?string $value = null): void {
    $button = new Button($text, $image, $value);
    $button->setIdentifier($value);

    $this->buttons[] = $button;
  }

  public function appendOptions(string ...$options): void {
    foreach ($options as $option) {
      $this->buttons[] = new Button($option);
    }
  }

  public function appendButtons(Button ...$buttons): void {
    foreach ($buttons as $button) {
      $this->buttons[] = $button;
    }
  }

  final public function handleResponse(Player $player, mixed $data): void {
    match (true) {
      is_null($data) => $this->onClose?->__invoke($player),
      default => $this->onSubmit?->__invoke($player, $this->getButton($data)),
      };
    }


    private function getButton(int $value): Button {
      return $this->buttons[$value];
    }

    protected function getType(): string {
      return 'form';
    }

    protected function serializeFormData(): array {
      return [
        'buttons' => (array) $this->buttons,
        'content' => (string) $this->content,
      ];
    }
  }