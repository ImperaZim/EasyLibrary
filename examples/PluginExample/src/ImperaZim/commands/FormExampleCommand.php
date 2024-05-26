<?php

declare(strict_types = 1);

namespace ImperaZim\commands;

use pocketmine\player\Player;

use ImperaZim\PluginExample;
use internal\commando\BaseCommand;
use ImperaZim\commands\subcommands\LongFormExampleSubcommand;
/**
* Class FormExampleCommand
* @package commands
*/
final class FormExampleCommand extends BaseCommand {

  /**
  * Get the command base
  * @return FormExampleCommand
  */
  public static function base() : self {
    return new self(
      plugin: PluginExample::getInstance(),
      name: PluginExample::getSettings(
        'form_command_name',
        ['form']
      )[0],
      description: PluginExample::getSettings(
        'form_command_description',
        '§7Form example command!'
      ),
      aliases: PluginExample::getSettings(
        'form_command_name',
        ['form']
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
    $this->registerSubCommand(LongFormExampleSubcommand::base());
  }

  /**
  * Executes the command.
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
      foreach ($this->getSubCommands() as $key => $subcommand) {
        $player->sendMessage('§e» §r/' . $this->getName() . ' ' . $subcommand->getName());
      }
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }
}