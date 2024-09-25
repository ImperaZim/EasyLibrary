<?php

declare(strict_types = 1);

namespace imperazim\components\command;

use imperazim\vendor\commando\BaseCommand;
use imperazim\vendor\commando\BaseSubCommand;
use imperazim\components\plugin\PluginToolkit;
use imperazim\components\command\subcommmand\Subcommand;

/**
* Class Command
* @package imperazim\components\command
*/
abstract class Command extends BaseCommand {

  /** @var CommandBuilder|null */
  private $build = null;

  /**
  * Command constructor.
  */
  public function __construct(PluginToolkit $plugin) {
    $this->build = $this->build($plugin);
    parent::__construct(
      plugin: $plugin,
      names: $this->build->getNames(),
      description: $this->build->getDescription()
    );
  }

  /**
  * Set up the command.
  * @return CommandBuilder
  */
  protected abstract function build(PluginToolkit $plugin): CommandBuilder;

  /**
  * Executes the command logic when triggered.
  * @param PluginToolkit $plugin The plugin toolkit instance.
  * @param mixed $sender The sender of the command (could be a player, console, etc.).
  * @param string $aliasUsed The alias of the command that was used.
  * @param array $args The arguments passed with the command.
  */
  protected abstract function run(PluginToolkit $plugin, mixed $sender, string $aliasUsed, array $args): void;

  /**
  * Prepares the command for execution.
  */
  protected function prepare(): void {
    if ($this->build instanceof CommandBuilder) {
      $builder = $this->build;
      if ($builder->getPermission()) {
        $this->setPermission($builder->getPermission());
      }
      $this->registerArguments($builder->getArguments());
      $this->addConstraints($builder->getConstraints());
      if ($builder->getSubcommands()) {
        $subcommands = [];
        foreach ($builder->getSubcommands() as $subcommand) {
          $subcommands[] = new $subcommand($this->getOwningPlugin());
        }
        $this->registerSubcommands($subcommands);
      }
    }
  }

  /**
  * Executes the command.
  * @param mixed $sender
  * @param string $aliasUsed
  * @param array $args
  */
  public function onRun(mixed $sender, string $aliasUsed, array $args): void {
    $this->run($this->getOwningPlugin(), $sender, $aliasUsed, $args);
  }
}