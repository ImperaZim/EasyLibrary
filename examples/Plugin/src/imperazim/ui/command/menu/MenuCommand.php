<?php

declare(strict_types = 1);

namespace imperazim\ui\command\menu;

use pocketmine\player\Player;

use imperazim\vendor\commando\BaseCommand;
use imperazim\vendor\commando\constraint\InGameRequiredConstraint;

use imperazim\ui\UiManager;
use imperazim\ui\command\menu\subcommand\ChestMenuSubcommand;
use imperazim\ui\command\menu\subcommand\HopperMenuSubcommand;
use imperazim\ui\command\menu\subcommand\DoubleChestMenuSubcommand;

/**
* Class MenuCommand
* @package imperazim\ui\command\menu
*/
final class MenuCommand extends BaseCommand {

  /**
  * MenuCommand constructor.
  */
  public function __construct() {
    $file = UiManager::getFile('menus');
    parent::__construct(
      plugin: UiManager::getPlugin(),
      names: $file->get('menu_command.names', ['menu']),
      description: $file->get('menu_command.description', ''),
    );
  }

  /**
  * Prepares the command for execution.
  */
  protected function prepare(): void {
    $this->setPermission('default.permission');
    $this->addConstraints(
      new InGameRequiredConstraint($this)
    );
    $this->registerSubcommands([
      new ChestMenuSubcommand(),
      new HopperMenuSubcommand(),
      new DoubleChestMenuSubcommand()
    ]);
  }

  /**
  * Executes the command.
  * @param mixed $player
  * @param string $aliasUsed
  * @param array $args
  */
  public function onRun(mixed $player, string $aliasUsed, array $args): void {
    foreach ($this->getSubCommands() as $subcommand) {
      $player->sendMessage('§e/' . $subcommand->getName() . ': §7' . $subcommand->getUsage());
    }
  }
}