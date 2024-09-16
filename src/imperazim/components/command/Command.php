<?php

declare(strict_types = 1);

namespace imperazim\components\command;

use imperazim\vendor\commando\BaseCommand;
use imperazim\vendor\commando\BaseSubCommand;
use imperazim\components\plugin\PluginToolkit;

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
      if (is_dir($builder->getSubcommands())) {
        $subcommands = $this->loadSubcommands($this->getOwningPlugin());
        $this->registerSubcommands($subcommands);
      }
    }
  }

  /**
  * Load subcommands from the subcommands directory.
  * @param PluginToolkit $plugin
  * @return array
  */
  private function loadSubcommands(PluginToolkit $plugin): array {
    $builder = $this->build;
    $directory = $builder->getSubcommands();
    $subcommandInstances = [];

    if (is_dir($directory)) {
      $files = scandir($directory);

      foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
          $className = pathinfo($file, PATHINFO_FILENAME);
          require_once $directory . DIRECTORY_SEPARATOR . $file;

          if (class_exists($className)) {
            $subcommand = new $className($plugin);

            if ($subcommand instanceof BaseSubCommand) {
              $subcommandInstances[] = $subcommand;
            }
          }
        }
      }
    }

    return $subcommandInstances;
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