<?php

declare(strict_types = 1);

namespace internal\libform\types;

use Closure;
use internal\libform\Form;
use pocketmine\utils\Utils;
use pocketmine\player\Player;
use pocketmine\form\FormValidationException;

/**
* Class ModalForm
* @package internal\libform\types
*/
final class ModalForm extends Form {

  /**
  * ModalForm constructor.
  * @param string $title
  * @param string $content
  * @param Closure(Player, bool): mixed $onSubmit
  * @param string $button1
  * @param string $button2
  */
  public function __construct(
    public string $title,
    protected string $content = '',
    private ?Closure $onSubmit = null,
    public string $button1 = 'gui.yes',
    public string $button2 = 'gui.no',
  ) {
    parent::__construct($title);
  }

  /**
  * Set the callback to be executed when the form is submitted.
  * @param Closure $closure @phpstan-param Closure(Player, bool): void $closure
  */
  public function onSubmit(Closure $closure): void {
    $this->onSubmit = $closure;
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
  * Set the text for button 1.
  * @param string $button1
  */
  public function setButton1(string $button1): void {
    $this->button1 = $button1;
  }

  /**
  * Set the text for button 2.
  * @param string $button2
  */
  public function setButton2(string $button2): void {
    $this->button2 = $button2;
  }

  /**
  * Create a confirmation modal form with a single confirm action.
  * @param string $title
  * @param string $content
  * @param Closure $onConfirm @phpstan-param Closure(Player): void $onConfirm
  * @return self
  */
  public static function confirm(string $title, string $content, Closure $onConfirm): self {
    Utils::validateCallableSignature(function(Player $player) {}, $onConfirm);
    return new self(
      $title,
      $content,
      static function(Player $player, bool $response) use ($onConfirm): void {
        if ($response) {
          $onConfirm($player);
        }
      }
    );
  }

  /**
  * Handle the response from the form.
  * @param Player $player
  * @param mixed $data
  * @throws FormValidationException
  */
  final public function handleResponse(Player $player,
    mixed $data): void {
    if (!is_bool($data)) {
      throw new FormValidationException('Expected bool, got ' . gettype($data));
    }
    if ($this->onSubmit !== null) {
      ($this->onSubmit)($player, $data);
    }
  }

  /**
  * Get the type of the form.
  * @return string
  */
  protected function getType(): string {
    return 'modal';
  }

  /**
  * Serialize the form data for transmission.
  * @return array
  */
  protected function serializeFormData(): array {
    return [
      'content' => $this->content,
      'button1' => $this->button1,
      'button2' => $this->button2,
    ];
  }

}