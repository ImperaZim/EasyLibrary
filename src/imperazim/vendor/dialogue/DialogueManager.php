<?php

declare(strict_types = 1);

namespace imperazim\vendor\dialogue;

use Generator;
use pocketmine\Server;
use BadMethodCallException;
use SOFe\AwaitGenerator\Await;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\PluginComponent;
use imperazim\components\plugin\traits\PluginComponentsTrait;

use imperazim\vendor\dialogue\Dialogue;
use imperazim\vendor\dialogue\types\AsyncDialogue;
use imperazim\vendor\dialogue\player\PlayerManager;
use imperazim\vendor\dialogue\exception\DialogueException;
use imperazim\vendor\dialogue\textures\DefaultDialogueTexture;

/**
* Class DialogueManager
* @package imperazim\vendor\dialogue
*/
final class DialogueManager extends PluginComponent {
  use PluginComponentsTrait;

  /** @var PlayerManager|null */
  private static ?PlayerManager $manager = null;

  /**
  * Initializes the dialogue component.
  * @param PluginToolkit $plugin The Plugin.
  */
  public static function init(PluginToolkit $plugin): array {
    self::$manager = new PlayerManager();
    self::$manager->init($plugin);

    self::setPlugin(plugin: $plugin);
    return [];
  }

  /**
  * Send the dialogue to a player.
  * @param Player $player
  * @param Dialogue $dialogue
  * @param bool $update_existing
  */
  public static function send(Player $player, Dialogue $dialogue, bool $update_existing = false) : void {
    self::$manager !== null || throw new BadMethodCallException("Dialog is not registered");
    self::$manager->getPlayer($player)->sendDialogue($dialogue, $update_existing);
  }

  /**
  * Requests a dialogue interaction with an NPC for a player.
  * @param Player $player The player requesting the dialogue interaction.
  * @param string $name The name of the dialogue.
  * @param string $text The text displayed in the dialogue.
  * @param DialogueTexture|null $texture The texture of the NPC.
  * @param array $buttons The buttons available in the dialogue.
  * @param array|null $button_mapping Optional mapping of button indices to their corresponding actions.
  * @param bool $update_existing Whether to update an existing dialogue if one is already active.
  * @return Generator A generator that yields the result of the dialogue request.
  */
  public static function request(Player $player, string $name, string $text, ?DialogueTexture $texture = null, array $buttons = [], ?array $button_mapping = null, bool $update_existing = false) : Generator {
    self::$manager !== null || throw new BadMethodCallException("Dialog is not registered");
    $instance = self::$manager->getPlayerNullable($player) ?? throw new DialogueException("Player is not connected", DialogueException::ERR_PLAYER_DISCONNECTED);
    $texture = $texture ?? new DefaultDialogueTexture(DefaultDialogueTexture::TEXTURE__10);
    $button_mapping = $button_mapping ?? array_keys($buttons);
    return yield from Await::promise(static fn(Closure $resolve, Closure $reject) => $instance->sendDialogue(new AsyncDialogue($name, $text, $texture, array_values($buttons), $button_mapping, $resolve, $reject), $update_existing));
  }

  /**
  * Removes the current NPC dialogue for a player.
  * @param Player $player The player whose NPC dialogue should be removed.
  * @return Dialogue|null The removed NPC dialogue, or null if none was removed.
  */
  public static function remove(Player $player) : ?Dialogue {
    self::$manager !== null || throw new BadMethodCallException("Dialog is not registered");
    return self::$manager->getPlayerNullable($player)?->removeCurrentDialogue()?->dialogue;
  }


}