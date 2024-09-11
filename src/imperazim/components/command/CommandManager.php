<?php

declare(strict_types = 1);

namespace imperazim\components\command;

use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\PluginComponent;
use imperazim\components\plugin\traits\PluginComponentsTrait;

use imperazim\components\command\defaults\VersionCommand;
use imperazim\components\command\defaults\GeneratePluginCommand;

/**
* Class CommandManager
* @package imperazim\components\command
*/
final class CommandManager extends PluginComponent {
  use PluginComponentsTrait;

  /**
  * Initializes the command component.
  * @param PluginToolkit $plugin The Plugin.
  */
  public static function init(PluginToolkit $plugin): array {
    self::setPlugin(plugin: $plugin);
    $plugin->overwriteCommands([
      'version' => new VersionCommand(),
      'genplugin' => new GeneratePluginCommand(),
    ]);
    return [];
  }

}