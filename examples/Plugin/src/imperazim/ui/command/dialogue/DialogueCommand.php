<?php

declare(strict_types = 1);

namespace imperazim\ui\command\dialogue;

use pocketmine\player\Player;

use imperazim\vendor\commando\BaseCommand;
use imperazim\vendor\commando\constraint\InGameRequiredConstraint;

use imperazim\ui\UiManager;
use imperazim\ui\command\dialogue\subcommand\SimpleDialogueSubcommand;

/**
* Class DialogueCommand
* @package imperazim\ui\command\dialogue
*/
final class DialogueCommand extends BaseCommand {

  /**
  * DialogueCommand constructor.
  */
  public function __construct() {
    $file = UiManager::getFile('dialogues');
    parent::__construct(
      plugin: UiManager::getPlugin(),
      names: $file->get('dialogue_command.names', ['dialogue']),
      description: $file->get('dialogue_command.description', ''),
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
      new SimpleDialogueSubcommand()
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