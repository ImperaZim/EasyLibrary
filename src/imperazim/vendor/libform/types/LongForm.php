<?php

declare(strict_types = 1);

namespace imperazim\vendor\libform\types;

use Closure;
use pocketmine\player\Player;
use pocketmine\form\FormValidationException;

use imperazim\vendor\libform\Form;
use imperazim\vendor\libform\elements\Title;
use imperazim\vendor\libform\elements\Image;
use imperazim\vendor\libform\elements\Button;
use imperazim\vendor\libform\elements\Content;
use imperazim\vendor\libform\interaction\ButtonResponse;

/**
* Class LongForm
* @package imperazim\vendor\libform\types
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
    public ?Title $title = new Title(''),
    protected ?Content $content = new Content(''),
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
  * @return Content|null
  */
  public function getContent(): ?Content {
    return $this->content;
  }

  /**
  * Set the content of the form.
  * @param Content $content
  * @return self
  */
  public function setContent(?Content $content = new Content('')): self {
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
  * @param int $identifier
  * @return Button
  * @throws FormValidationException
  */
  private function getButton(int $identifier): Button {
    if (!isset($this->buttons[$identifier])) {
      throw new FormValidationException("Button at index $identifier does not exist.");
    }
    return $this->buttons[$identifier];
  }

  /**
  * Add a button to the form.
  * @param string $text
  * @param Image|null $image
  * @param string|null $identifier
  * @return self
  */
  public function addButton(string $text, ?Image $image = null, ?string $identifier = null, ?ButtonResponse $response = null): self {
    $button = new Button($text, $image, $identifier, $response);
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