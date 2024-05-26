<?php

declare(strict_types = 1);

namespace ImperaZim\commands\subcommands;

use pocketmine\player\Player;

use ImperaZim\PluginExample;
use internal\command\BaseSubCommand;
use ImperaZim\forms\ExampleLongForm;

/**
* Class LongFormExampleSubcommand
* @package ImperaZim\commands\subcommands
*/
final class LongFormExampleSubcommand extends BaseSubCommand {

  /**
  * Get the subcommand base
  * @return LongFormExampleSubcommand
  */
  public static function base() : self {
    return new self(
      plugin: PluginExample::getInstance(),
      name: 'long',
      description: '§7LongForm.'
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
      new LongFormExampleSubcommand($player);
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }
}