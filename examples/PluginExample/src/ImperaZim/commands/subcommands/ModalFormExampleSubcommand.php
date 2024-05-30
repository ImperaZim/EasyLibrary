<?php

declare(strict_types = 1);

namespace ImperaZim\commands\subcommands;

use pocketmine\player\Player;

use ImperaZim\PluginExample;
use ImperaZim\forms\ExampleModalForm;
use internal\commando\BaseSubCommand;

/**
* Class ModalFormExampleSubcommand
* @package ImperaZim\commands\subcommands
*/
final class ModalFormExampleSubcommand extends BaseSubCommand {

  /**
  * Get the subcommand base
  * @return ModalFormExampleSubcommand
  */
  public static function base() : self {
    return new self(
      plugin: PluginExample::getInstance(),
      name: 'modal',
      description: '§7ModalForm.'
    );
  }

  /**
  * Get the command permission
  */
  public function getPermission() {
    return 'plugin.permission';
  }

  /**
  * Prepares the sub for execution.
  */
  protected function prepare(): void {
    $this->setPermission($this->getPermission());
  }

  /**
  * Executes the subcommand.
  *
  * @param mixed $player
  * @param string $aliasUsed
  * @param array $args
  */
  public function onRun(mixed $player, string $aliasUsed, array $args): void {
    try {
      if (!$player instanceof Player) {
        $this->sendConsoleError();
        return;
      }
      new ModalFormExampleSubcommand($player);
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }
}