<?php

declare(strict_types = 1);

namespace internal\dialogue\types;

use Closure;
use pocketmine\player\Player;
use internal\dialogue\Dialogue;
use internal\dialogue\DialogueHooker;
use internal\dialogue\elements\DialogueButton;
use internal\dialogue\textures\DialogueTexture;
use internal\dialogue\textures\DialogueTextureTypes;
use internal\dialogue\textures\DialogueTextureOffset;
use internal\dialogue\textures\PlayerDialogueTexture;
use internal\dialogue\textures\EntityDialogueTexture;
use internal\dialogue\textures\DefaultDialogueTexture;

final class SimpleDialogue extends Dialogue {
  
  /** @var DialogueTexture */ 
  private ?DialogueTexture $dialogueTexture = null;

  /**
  * SimpleDialogue Constructor.
  * @param string|null $name The name of the dialogue.
  * @param string|null $text The text displayed in the dialogue.
  * @param array|null $texture The texture of the dialogue.
  * @param array|null $buttons The buttons of the dialogue.
  * @param Closure|null $onResponse The action to be executed when the player responds.
  * @param Closure|null $onClose The action to be executed when the dialogue is closed.
  * @param Closure|null $onInvalidResponse The action to be executed when the player's response is invalid.
  * @param Closure|null $onDisconnect The action to be executed when the player disconnects.
  */
  public function __construct(
    private ?string $name = "Default name",
    private ?string $text = "Default text",
    private ?array $texture = [],
    private ?array $buttons = [],
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

  /**
  * Gets the text of the dialogue.
  * @return string
  */
  public function getText() : string {
    return $this->text;
  }

  /**
  * Sets the text of the dialogue.
  * @param string $text The text of the dialogue.
  * @return self
  */
  public function setText(string $text) : self {
    $this->text = $text;
    return $this;
  }

  /**
  * Loads the texture of the dialogue.
  * @param array $texture The texture of the dialogue.
  */
  public function loadTexture(array $texture) : void {
    $textureTypes = [
      DialogueTextureTypes::ENTITY => EntityDialogueTexture::class,
      DialogueTextureTypes::SKIN => PlayerDialogueTexture::class,
      DialogueTextureTypes::DEFAULT => DefaultDialogueTexture::class
    ];
    if (isset($texture['type'], $texture['data'])) {
      $textureType = $texture['type'];

      if (array_key_exists($textureType, $textureTypes)) {
        $offset = null;
        $textureClass = $textureTypes[$textureType];
        if ($textureType === DialogueTextureTypes::SKIN && isset($texture['offset'])) {
          $parent = DialogueTextureOffset::defaultPlayerPortrait();
          $offset = new DialogueTextureOffset(
            2.0,
            2.0,
            2.0,
            $parent->translate_x,
            $parent->translate_y,
            $parent->translate_z
          );
        }
        $this->setTexture(new $textureClass($texture['data'], null, $offset));
      } else {
        $this->setTexture(new DefaultDialogueTexture(DefaultDialogueTexture::TEXTURE__10));
      }
    } else {
      $this->setTexture(new DefaultDialogueTexture(DefaultDialogueTexture::TEXTURE__10));
    }
  }

  /**
  * Gets the texture of the dialogue.
  * @return DialogueTexture The texture of the dialogue.
  */
  public function getTexture() : DialogueTexture {
    return $this->dialogueTexture;
  }

  /**
  * Sets the texture of the dialogue.
  * @param DialogueTexture $texture The texture of the dialogue.
  * @return self
  */
  public function setTexture(DialogueTexture $texture) : self {
    $this->dialogueTexture = $texture;
    return $this;
  }

  /**
  * Gets the buttons of the dialogue.
  * @return array The buttons of the dialogue.
  */
  public function getButtons() : array {
    return $this->buttons;
  }

  /**
  * Adds the buttons of the dialogue.
  * @param array $buttons The buttons of the dialogue.
  * @return self
  */
  public function addButtons(array $buttons) : self {
    $this->buttons = $buttons;
    return $this;
  }

  /**
  * Gets the button from id.
  * @return DialogueButton|null The button.
  */
  public function getButton(int $id) : ?DialogueButton {
    return $this->buttons[$id] ?? null;
  }

  /**
  * Adds a button to the dialogue.
  * @param DialogueButton $button The button to be added.
  * @return self
  */
  public function addButton(DialogueButton $button) : self {
    $this->buttons[] = $button;
    return $this;
  }

  /**
  * Gets the player response listener.
  * @return Closure|null The action to be executed when the player responds.
  */
  public function getResponseListener(): ?Closure {
    return $this->onResponse;
  }

  /**
  * Sets the player response listener.
  * @param Closure|null $onResponse The action to be executed when the player responds.
  * @return self
  */
  public function setResponseListener(?Closure $onResponse) : self {
    $this->onResponse = $onResponse;
    return $this;
  }

  /**
  * Gets the dialogue close listener.
  * @return Closure|null The action to be executed when the dialogue is closed.
  */
  public function getCloseListener(): ?Closure {
    return $this->onClose;
  }

  /**
  * Sets the dialogue close listener.
  * @param Closure|null $onClose The action to be executed when the dialogue is closed.
  * @return self
  */
  public function setCloseListener(?Closure $onClose) : self {
    $this->onClose = $onClose;
    return $this;
  }

  /**
  * Responds when the player responds to the dialogue.
  * @param Player $player The player who responded.
  * @param int $id The index of the clicked button.
  */
  public function onPlayerRespond(Player $player, int $id) : void {
    $button = $this->getButton($id);
    $buttonResponse = $button->getResponse();
    var_dump($buttonResponse);
    if ($buttonResponse !== null) {
      $buttonResponse->runAt($player, $button);
    }
  }

  /**
  * Responds when the player closes the dialogue.
  * @param Player $player The player who closed the dialogue.
  * @return void
  */
  public function onPlayerClose(Player $player) : void {
    $response = $this->getCloseListener();
    if ($response !== null) {
      $response($player);
    }
  }

  /**
  * Responds when the player provides an invalid response.
  * @param Player $player The player who provided an invalid response.
  * @param int $invalidResponse The index of the invalid response.
  */
  public function onPlayerRespondInvalid(Player $player, int $invalidResponse) : void {
    if ($this->onInvalidResponse !== null) {
      ($this->onInvalidResponse)($player, $invalidResponse);
    }
  }

  /**
  * Responds when the player disconnect.
  * @param Player $player.
  */
  public function onPlayerDisconnect(Player $player) : void {
    if ($this->onDisconnect !== null) {
      ($this->onDisconnect)($player);
    }
  }
}