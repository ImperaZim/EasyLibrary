<?php

declare(strict_types = 1);

namespace ImperaZim\commands;

use ImperaZim\PluginExample;
use ImperaZim\menus\ExampleMenu;
use internal\commando\BaseCommand;

use pocketmine\player\Player;

/**
* Class MenuExampleCommand
* @package commands
*/
final class MenuExampleCommand extends BaseCommand {

  /**
  * Get the command base
  * @return MenuExampleCommand
  */
  public static function base() : self {
    return new self(
      plugin: PluginExample::getInstance(),
      name: PluginExample::getSettings(
        'menu_command_name',
        ['menu']
      )[0],
      description: PluginExample::getSettings(
        'menu_command_description',
        '§7Menu example command!'
      ),
      aliases: PluginExample::getSettings(
        'menu_command_name',
        ['menu']
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
    new ExampleMenu($player);
  }
}