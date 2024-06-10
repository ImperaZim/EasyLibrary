<?php

declare(strict_types = 1);

namespace internal\dialogue\types;

use Closure;
use pocketmine\player\Player;
use internal\dialogue\Dialogue;
use internal\dialogue\elements\DialogueButton;
use internal\dialogue\textures\DialogueTexture;
use internal\dialogue\textures\DialogueTextureTypes;
use internal\dialogue\textures\DialogueTextureOffset;
use internal\dialogue\textures\PlayerDialogueTexture;
use internal\dialogue\textures\EntityDialogueTexture;
use internal\dialogue\textures\DefaultDialogueTexture;

final class SimpleDialogue extends Dialogue {

  /**
  * @param string $name
  * @param string $text
  * @param DialogueTexture $texture
  * @param list<DialogueButton> $buttons
  * @param (Closure(Player, int) : void)|null $onResponse
  * @param (Closure(Player) : void)|null $onClose
  * @param (Closure(Player, int) : void)|null $onInvalidResponse
  * @param (Closure(Player) : void)|null $onDisconnect
  */
  public function __construct(
    private ?string $name = "Default name",
    private ?string $text = "Default text",
    private ?array $texture = [],
    private ?array $buttons = [],
    private ?Closure $onResponse = null,
    private ?Closure $onClose = null,
    private ?Closure $onInvalidResponse = null,
    private ?Closure $onDisconnect = null
  ) {
    if ($texture !== null && isset($texture['type']) && isset($texture['data'])) {
      $this->loadTexture($texture);
    } else {
      $id = DefaultDialogueTexture::TEXTURE__10;
      $this->setTexture(new DefaultDialogueTexture($id));
    }
    parent::__construct($name);
  }

  public function setName(string $name) : self {
    $this->name = $name;
    return $this;
  }

  public function setText(string $text) : self {
    $this->text = $text;
    return $this;
  }

  public function loadTexture(array $texture) : void {
    switch ($texture['type']) {
      case DialogueTextureTypes::ENTITY:
        $this->setTexture(new EntityDialogueTexture($texture['data']));
        break;
      case DialogueTextureTypes::SKIN:
        if (!isset($texture['offset'])) {
          $this->setTexture(new PlayerDialogueTexture($texture['data']));
        } else {
          $parent = DialogueTextureOffset::defaultPlayerPortrait();
          $offset = new DialogueTextureOffset(2.0, 2.0, 2.0, $parent->translate_x, $parent->translate_y, $parent->translate_z);
          $this->setTexture(new PlayerDialogueTexture($texture['data'], null, $offset));
        }
        break;

      case DialogueTextureTypes::DEFAULT:
      default:
        $id = DefaultDialogueTexture::TEXTURE__10;
        $this->setTexture(new DefaultDialogueTexture($id));
        break;
    }
    return $this;
  }

  public function getTexture() : DialogueTexture {
    return $this->texture;
  }

  public function setTexture(DialogueTexture $texture) : self {
    $this->texture = $texture;
    return $this;
  }

  public function getButtons() : array {
    return $this->buttons;
  }

  /**
  * @param list<DialogueButton> $buttons
  * @return self
  */
  public function setButtons(array $buttons) : self {
    $this->buttons = $buttons;
    return $this;
  }

  /**
  * @param DialogueButton $name
  * @param (Closure(Player) : void)|null $on_click
  * @return self
  */
  public function addButton(DialogueButton $button) : self {
    $this->buttons[] = $button;
    return $this;
  }

  /**
  * @param (Closure(Player, int) : void)|null $onResponse
  * @return self
  */
  public function setResponseListener(?Closure $onResponse) : self {
    $this->onResponse = $onResponse;
    return $this;
  }

  /**
  * @param (Closure(Player) : void)|null $onClose
  * @return self
  */
  public function setCloseListener(?Closure $onClose) : self {
    $this->onClose = $onClose;
    return $this;
  }

  public function onPlayerRespond(Player $player, int $button) : void {
    $this->buttons[$button]->onClick($player);
    if ($this->onResponse !== null) {
      ($this->onResponse)($player, $button);
    }
  }

  public function onPlayerRespondInvalid(Player $player, int $invalid_response) : void {
    if ($this->onInvalidResponse !== null) {
      ($this->onInvalidResponse)($player, $invalid_response);
    }
  }

  public function onPlayerClose(Player $player) : void {
    if ($this->onClose !== null) {
      ($this->onClose)($player);
    }
  }

  public function onPlayerDisconnect(Player $player) : void {
    if ($this->onDisconnect !== null) {
      ($this->onDisconnect)($player);
    }
  }
}