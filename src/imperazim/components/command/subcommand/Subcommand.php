<?php

declare(strict_types = 1);

namespace imperazim\components\command\subcommand;

use imperazim\vendor\commando\BaseSubCommand;
use imperazim\components\plugin\PluginToolkit;

/**
* Class Command
* @package imperazim\components\command\subcommand
*/
abstract class Subcommand extends BaseSubCommand {

  /** @var SubcommandBuilder|null */
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
  * @return SubcommandBuilder
  */
  protected abstract function build(PluginToolkit $plugin): SubcommandBuilder;

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
    if ($this->build instanceof SubcommandBuilder) {
      $builder = $this->build;
      if ($builder->getPermission()) {
        $this->setPermission($builder->getPermission());
      }
      $this->registerArguments($builder->getArguments());
      $this->addConstraints($builder->getConstraints());
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