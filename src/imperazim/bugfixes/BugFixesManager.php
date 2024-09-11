<?php

declare(strict_types = 1);

namespace imperazim\bugfixes;

use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\PluginComponent;
use imperazim\components\plugin\traits\PluginComponentsTrait;

/**
* Class BugFixesManager
* @package imperazim\bugfixes
*/
final class BugFixesManager extends PluginComponent {
  use PluginComponentsTrait;

  /**
  * Initializes the BugFixesManager component.
  * @param PluginToolkit $plugin The Plugin.
  */
  public static function init(PluginToolkit $plugin): array {
    return [
      self::LISTENER_COMPONENT => [
        new BugFixesListener()
      ],
    ];
  }

}