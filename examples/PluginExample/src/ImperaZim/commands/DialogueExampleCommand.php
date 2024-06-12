<?php

declare(strict_types = 1);

namespace ImperaZim\commands;

use ImperaZim\PluginExample;
use internal\commando\BaseCommand;
use ImperaZim\dialogues\ExampleDialogue;

use pocketmine\player\Player;

/**
* Class DialogueExampleCommand
* @package commands
*/
final class DialogueExampleCommand extends BaseCommand {

  /**
  * Get the command base
  * @return DialogueExampleCommand
  */
  public static function base() : self {
    return new self(
      plugin: PluginExample::getInstance(),
      names: PluginExample::getSettings(
        'dialogue_command_name',
        ['dialogue']
      ),
      description: PluginExample::getSettings(
        'dialogue_command_description',
        '§7Dialogue example command!'
      )
    );
  }
  
  /**
   * Get the command permission
   */
  public function getPermission() {
    return 'plugin.permission';
  }

  /**
  * Prepares the command for execution.
  */
  protected function prepare(): void {
    $this->setPermission($this->getPermission());
  }

  /**
  * Executes the command.
  * @param mixed $player
  * @param string $aliasUsed
  * @param array $args
  */
  public function onRun(mixed $player, string $aliasUsed, array $args): void {
    if (!$player instanceof Player) {
      $this->sendConsoleError();
      return;
    }
    new ExampleDialogue($player);
  }
}