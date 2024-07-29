<?php

declare(strict_types = 1);

namespace imperazim\ui\command\menu\subcommand;

use pocketmine\player\Player;

use imperazim\ui\UiManager;
use imperazim\ui\menu\HopperMenuExample;

use imperazim\vendor\commando\BaseSubCommand;
use imperazim\vendor\commando\constraint\InGameRequiredConstraint;

/**
* Class HopperMenuSubCommand
* @package imperazim\ui\command\menu\subcommand
*/
final class HopperMenuSubCommand extends BaseSubCommand {

  /**
  * HopperMenuSubCommand constructor.
  */
  public function __construct() {
    $file = UiManager::getFile('menus');
    parent::__construct(
      plugin: UiManager::getPlugin(),
      names: $file->get('hopper_menu_subcommand.names', ['hopper']),
      description: $file->get('hopper_menu_subcommand.description', ''),
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
    new HopperMenuExample($player, []);
  }
}