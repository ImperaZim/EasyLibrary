<?php

declare(strict_types = 1);

namespace internal\dialogue;

use BadMethodCallException;
use pocketmine\player\Player;
use internal\dialogue\Dialogue;
use pocketmine\plugin\PluginBase;
use internal\dialogue\player\PlayerManager;

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
		self::$manager === null || throw new BadMethodCallException("NpcDialog is already registered");
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
		self::$manager !== null || throw new BadMethodCallException("NpcDialog is not registered");
		self::$manager->getPlayer($player)->sendDialogue($dialogue, $update_existing);
	}
	
}