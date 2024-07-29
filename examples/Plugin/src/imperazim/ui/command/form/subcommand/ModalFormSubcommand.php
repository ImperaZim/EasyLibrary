<?php

declare(strict_types = 1);

namespace imperazim\ui\command\form\subcommand;

use pocketmine\player\Player;

use imperazim\ui\UiManager;
use imperazim\ui\form\ModalFormExample;

use imperazim\vendor\commando\BaseSubCommand;
use imperazim\vendor\commando\constraint\InGameRequiredConstraint;

/**
* Class ModalFormSubCommand
* @package imperazim\ui\command\form\subcommand
*/
final class ModalFormSubCommand extends BaseSubCommand {

  /**
  * ModalFormSubCommand constructor.
  */
  public function __construct() {
    $file = UiManager::getFile('forms');
    parent::__construct(
      plugin: UiManager::getPlugin(),
      names: $file->get('modal_form_subcommand.names', ['modal']),
      description: $file->get('modal_form_subcommand.description', ''),
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
    new ModalFormExample($player, []);
  }
}