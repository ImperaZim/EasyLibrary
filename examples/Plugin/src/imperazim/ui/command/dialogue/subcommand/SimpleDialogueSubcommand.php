<?php

declare(strict_types = 1);

namespace imperazim\ui\command\dialogue\subcommand;

use pocketmine\player\Player;

use imperazim\ui\UiManager;
use imperazim\ui\dialogue\SimpleDialogueExample;

use imperazim\vendor\commando\BaseSubCommand;
use imperazim\vendor\commando\constraint\InGameRequiredConstraint;

/**
* Class LongFormSubCommand
* @package imperazim\ui\command\dialogue\subcommand
*/
final class LongFormSubCommand extends BaseSubCommand {

  /**
  * LongFormSubCommand constructor.
  */
  public function __construct() {
    $file = UiManager::getFile('dialogues');
    parent::__construct(
      plugin: UiManager::getPlugin(),
      names: $file->get('simple_dialogue_subcommand.names', ['simple']),
      description: $file->get('simple_dialogue_subcommand.description', ''),
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
    new SimpleDialogueExample($player, []);
  }
}