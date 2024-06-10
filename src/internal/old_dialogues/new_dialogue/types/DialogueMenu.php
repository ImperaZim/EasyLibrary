<?php

declare(strict_types = 1);

namespace internal\dialogue\types;

use Closure;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

use internal\dialogue\Dialogue;
use internal\dialogue\dialogue\texture\DialogueTextureTypes;
use internal\dialogue\dialogue\texture\DialogueTextureOffset;
use internal\dialogue\dialogue\texture\DefaultDialogueTexture;

final class DialogueMenu {

  /**
  * @param string|null $name
  * @param string|null $text
  * @param array|null $texture
  * @param list<DialogueButton> $buttons
  * @param (Closure(Player, int) : void)|null $onRespond
  * @param (Closure(Player) : void)|null $onClose
  */
  private function __construct(
    public ?string $name = "Default name",
    public ?string $text = "Default text",
    public ?array $texture = [],
    public ?array $buttons = [],
    public ?Closure $onRespond = null,
    public ?Closure $onClose = null
  ) {
    parent::__construct($name);
    $this->setText($text);
    if ($texture !== null && isset($texture['type']) && isset($texture['data'])) {
      switch ($texture['type']) {
        case DialogueTextureTypes::ENTITY:
          $this->setEntityTexture($texture['data']);
          break;
        case DialogueTextureTypes::SKIN:
          if (!isset($texture['offset'])) {
            $this->setSkinTexture($texture['data']);
          } else {
            $parent = DialogueTextureOffset::defaultPlayerPortrait();
            $offset = new DialogueTextureOffset(2.0, 2.0, 2.0, $parent->translate_x, $parent->translate_y, $parent->translate_z);
            $this->setSkinTexture($texture['data'], null, $offset);
          }
          break;

        case DialogueTextureTypes::DEFAULT:
        default:
          $texture = DefaultDialogueTexture::TEXTURE__10;
          $this->setDefaultTexture($texture);
          break;
      }
    } else {
      $texture = DefaultDialogueTexture::TEXTURE__10;
      $this->setDefaultTexture($texture);
    }
    if (!empty($buttons)) {
      foreach ($buttons as $button) {
        $this->addSimpleButton($button);
      }
    }
    if ($onRespond !== null) {
      $this->setResponseListener($onRespond);
    }
    if ($onClose !== null) {
      $this->setCloseListener($onClose);
    }
  }

}