<?php

declare(strict_types = 1);

namespace internal\dialogue;

use Closure;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

use internal\dialogue\DialogueHooker;
use internal\dialogue\dialogue\Dialogue;
use internal\dialogue\dialogue\DialogueButton;
use internal\dialogue\dialogue\SimpleDialogue;
use internal\dialogue\dialogue\SimpleDialogueButton;
use internal\dialogue\dialogue\texture\DialogueTexture;
use internal\dialogue\dialogue\texture\DialogueTextureOffset;
use internal\dialogue\dialogue\texture\PlayerDialogueTexture;
use internal\dialogue\dialogue\texture\EntityDialogueTexture;
use internal\dialogue\dialogue\texture\DefaultDialogueTexture;

class Dialogue {

  /**
  * @param string|null $name
  */
  private function __construct(
    private ?string $name = "Default name",
  ) {}

  public function setName(string $name) : self {
    $this->name = $name;
    return $this;
  }

  public function setText(string $text) : self {
    $this->text = $text;
    return $this;
  }

  public function setTexture(DialogueTexture $texture) : self {
    $this->texture = $texture;
    return $this;
  }

  /**
  * @param DefaultDialogueTexture::TEXTURE_* $id
  * @return self
  */
  public function setDefaultTexture(int $id) : self {
    return $this->setTexture(new DefaultDialogueTexture($id));
  }

  /**
  * @param EntityIds::*|string $entity_id
  * @return self
  */
  public function setEntityTexture(string $entity_id) : self {
    return $this->setTexture(new EntityDialogueTexture($entity_id));
  }

  public function setSkinTexture(Skin $skin, ?DialogueTextureOffset $picker_offset = null, ?DialogueTextureOffset $portrait_offset = null) : self {
    return $this->setTexture(new PlayerDialogueTexture($skin, $picker_offset, $portrait_offset));
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
  * @param SimpleDialogueButton $name
  * @param (Closure(Player) : void)|null $on_click
  * @return self
  */
  public function addSimpleButton(SimpleDialogueButton $button) : self {
    $this->buttons[] = $button;
    return $this;
  }

  /**
  * @param (Closure(Player, int) : void)|null $on_respond
  * @return self
  */
  public function setResponseListener(?Closure $on_respond) : self {
    $this->on_respond = $on_respond;
    return $this;
  }

  /**
  * @param (Closure(Player) : void)|null $on_close
  * @return self
  */
  public function setCloseListener(?Closure $on_close) : self {
    $this->on_close = $on_close;
    return $this;
  }

  /**
  * Send the dialogue to a player.
  * @param Player $player
  * @param bool $update_existing
  */
  public function sendTo(Player $player, bool $update_existing = false) : void {
    $hooker = new DialogueHooker();
    $hooker->manager !== null || throw new BadMethodCallException("Dialog is not registered");
    $dialogue = new SimpleDialogue($this->name, $this->text, $this->texture, $this->buttons, $this->on_respond, $this->on_close);
    $hooker->manager->getPlayer($player)->sendDialogue($dialogue, $update_existing);
  }
}