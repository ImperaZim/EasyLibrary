<?php

declare(strict_types = 1);

namespace imperazim\components\command;

use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\PluginComponent;
use imperazim\components\plugin\traits\PluginComponentsTrait;

use imperazim\components\command\defaults\VersionCommand;

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
      'version' => new VersionCommand()
    ]);
    return [];
  }

}