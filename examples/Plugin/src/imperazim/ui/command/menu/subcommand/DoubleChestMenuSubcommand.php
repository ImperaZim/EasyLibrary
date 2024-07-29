<?php

declare(strict_types = 1);

namespace imperazim\ui\command\menu\subcommand;

use pocketmine\player\Player;

use imperazim\ui\UiManager;
use imperazim\ui\menu\DoubleChestMenuExample;

use imperazim\vendor\commando\BaseSubCommand;
use imperazim\vendor\commando\constraint\InGameRequiredConstraint;

/**
* Class DoubleChestMenuSubCommand
* @package imperazim\ui\command\menu\subcommand
*/
final class DoubleChestMenuSubCommand extends BaseSubCommand {

  /**
  * DoubleChestMenuSubCommand constructor.
  */
  public function __construct() {
    $file = UiManager::getFile('menus');
    parent::__construct(
      plugin: UiManager::getPlugin(),
      names: $file->get('double_chest_menu_subcommand.names', ['double']),
      description: $file->get('double_chest_menu_subcommand.description', ''),
    );
  }

  /**
  * Prepares the subcommand for execution.
  */
  protected function prepare(): void {
    $this->setPermission('default.permission');
    $this->addConstraints(
      new InGameRequiredConstraint($this)
    );
  }

  /**
  * Executes the subcommand.
  * @param mixed $player
  * @param string $aliasUsed
  * @param array $args
  */
  public function onRun(mixed $player, string $aliasUsed, array $args): void {
    new DoubleChestMenuExample($player, []);
  }
}