<?php

declare(strict_types = 1);

namespace ImperaZim\commands\subcommands;

use pocketmine\player\Player;

use ImperaZim\PluginExample;
use internal\commando\BaseSubCommand;
use ImperaZim\forms\ExampleCustomForm;

/**
* Class CustomFormExampleSubcommand
* @package ImperaZim\commands\subcommands
*/
final class CustomFormExampleSubcommand extends BaseSubCommand {

  /**
  * Get the subcommand base
  * @return CustomFormExampleSubcommand
  */
  public static function base() : self {
    return new self(
      plugin: PluginExample::getInstance(),
      names: ['custom'],
      description: '§7CustomForm.'
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
      new ExampleCustomForm($player);
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }
}