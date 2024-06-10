<?php

declare(strict_types = 1);

namespace internal\dialogue;

use Generator;
use BadMethodCallException;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;
use internal\dialogue\Dialogue;
use pocketmine\plugin\PluginBase;
use internal\dialogue\types\AsyncDialogue;
use internal\dialogue\player\PlayerManager;
use internal\dialogue\exception\DialogueException;
use internal\dialogue\textures\DefaultDialogueTexture;

/**
* Class DialogueHooker
* @package internal\dialogue
*/
final class DialogueHooker {
  
  /** @var PlayerManager|null */
  private static ?PlayerManager $manager = null;

  /**
  * DialogueHooker constructor.
  * @param PluginBase|null $registrant Plugin registrant.
  */
  public function __construct(private ?PluginBase $registrant = null) {
    if ($registrant != null) {
      self::register($registrant);
    }
  }

  /**
  * Checks if the dialogue is registered.
  * @return bool Returns true if the dialogue is registered, false otherwise.
  */
  public static function isRegistered() : bool{
		return self::$manager !== null;
	}


  /**
  * Registers the dialogue.
  * @throws BadMethodCallException If the dialogue is already registered.
  */
	public static function register(?PluginBase $plugin) : void {
		self::$manager === null || throw new BadMethodCallException("Dialog is already registered");
		self::$manager = new PlayerManager();
		self::$manager->init($plugin);
	}
	
	/**
  * Send the dialogue to a player.
  * @param Player $player
  * @param Dialogue $dialogue
  * @param bool $update_existing
  */
	public static function send(Player $player, Dialogue $dialogue, bool $update_existing = false) : void{
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
    $texture ??= new DefaultDialogueTexture(DefaultDialogueTexture::TEXTURE__10);
    $button_mapping ??= array_keys($buttons);
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