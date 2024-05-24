<?php

declare(strict_types = 1);

namespace internal\libform\types;

use Closure;
use pocketmine\player\Player;
use pocketmine\form\FormValidationException;

use internal\libform\elements\Image;
use internal\libform\elements\Button;
use internal\libform\handler\ButtonResponse;

/**
* Class LongForm
* @package internal\libform\types
*/
final class LongForm extends Form {

  /**
  * LongForm constructor.
  * @param string $title
  * @param string $content
  * @param Button[] $buttons
  * @param (Closure(Player): mixed)|null $onClose
  */
  public function __construct(
    public string $title,
    protected string $content = '',
    private array $buttons = [],
    private ?Closure $onClose = null,
  ) {
    parent::__construct($title);
  }

  /**
  * Set a callback to be executed when the form is closed.
  * @param Closure(Player): void $closure
  */
  public function onClose(Closure $closure): void {
    $this->onClose = $closure;
  }

  /**
  * Get the content of the form.
  * @return string
  */
  public function getContent(): string {
    return $this->content;
  }

  /**
  * Set the content of the form.
  * @param string $content
  */
  public function setContent(string $content): void {
    $this->content = $content;
  }

  /**
  * Add a button to the form.
  * @param string $text
  * @param Image|null $image
  * @param string|null $value
  */
  public function addButton(string $text, ?Image $image = null, ?string $value = null, ?ButtonResponse $response = null): void {
    $button = new Button($text, $image, $value, $response);
    $button->setIdentifier($value);
    $this->buttons[] = $button;
  }

  /**
  * Append multiple string options as buttons to the form.
  * @param string ...$options
  */
  public function appendOptions(string ...$options): void {
    foreach ($options as $option) {
      $this->buttons[] = new Button($option);
    }
  }

  /**
  * Append multiple Button objects to the form.
  * @param Button ...$buttons
  */
  public function appendButtons(Button ...$buttons): void {
    $this->buttons = array_merge($this->buttons, $buttons);
  }

  /**
  * Handle the response from the form.
  * @param Player $player
  * @param mixed $data
  */
  final public function handleResponse(Player $player, mixed $data): void {
    if (is_null($data)) {
      if ($this->onClose !== null) {
        ($this->onClose)($player);
      }
    } else {
      $button = $this->getButton($data);
      $buttonResponse = $button->getResponse();
      if ($buttonResponse !== null) {
        $buttonResponse->runAt($player, $button);
      }
      if ($button->getShouldReopen()) {
        $this->sendTo($player);
      }
    }
  }


  /**
  * Get a button by its index.
  * @param int $value
  * @return Button
  * @throws FormValidationException
  */
  private function getButton(int $value): Button {
    if (!isset($this->buttons[$value])) {
      throw new FormValidationException("Button at index $value does not exist.");
    }
    return $this->buttons[$value];
  }

  /**
  * Get the type of the form.
  * @return string
  */
  protected function getType(): string {
    return 'form';
  }

  /**
  * Serialize the form data for transmission.
  * @return array
  */
  protected function serializeFormData(): array {
    return [
      'buttons' => $this->buttons,
      'content' => $this->content,
    ];
  }

}