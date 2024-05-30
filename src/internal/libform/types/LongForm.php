<?php

declare(strict_types = 1);

namespace internal\libform\types;

use Closure;
use pocketmine\player\Player;
use pocketmine\form\FormValidationException;

use internal\libform\Form;
use internal\libform\elements\Title;
use internal\libform\elements\Image;
use internal\libform\elements\Button;
use internal\libform\elements\Content;
use internal\libform\interaction\ButtonResponse;

/**
* Class LongForm
* @package internal\libform\types
*/
final class LongForm extends Form {

  /**
  * LongForm constructor.
  * @param Title $title
  * @param Content $content
  * @param Button[] $buttons
  * @param (Closure(Player): mixed)|null $onClose
  */
  public function __construct(
    public ?Title $title,
    protected ?Content $content = '',
    private ?array $buttons = [],
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
  * @return self
  */
  public function setContent(string $content): self {
    $this->content = $content;
    return $this;
  }
  
   /**
  * Get the buttons of the form.
  * @return array
  */
  public function getButtons(): array {
    return $this->buttons;
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
  * Add a button to the form.
  * @param string $text
  * @param Image|null $image
  * @param string|null $value
  * @return self
  */
  public function addButton(string $text, ?Image $image = null, ?string $value = null, ?ButtonResponse $response = null): self {
    $button = new Button($text, $image, $value, $response);
    $button->setIdentifier($value);
    $this->buttons[] = $button;
    return $this;
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
      'buttons' => $this->getButtons(),
      'content' => $this->getContent()->getText(),
    ];
  }

}