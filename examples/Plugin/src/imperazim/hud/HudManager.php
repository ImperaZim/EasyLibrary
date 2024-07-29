<?php

declare(strict_types = 1);

namespace imperazim\hud;

use imperazim\hud\bossbar\BossBarTask;
use imperazim\hud\scoreboard\ScoreBoardTask;

use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\PluginComponent;
use imperazim\components\plugin\traits\PluginComponentsTrait;

/**
* Class HudManager
* @package imperazim\hud
*/
final class HudManager extends PluginComponent {
  use PluginComponentsTrait;

  /**
  * Initializes the hud component.
  * @param PluginToolkit $plugin The Plugin.
  */
  public static function init(PluginToolkit $plugin): array {
    /**
    * Define the main instance to make the code easier and there is no need to define getInstance in main if you are adding it just for that! However, it is not mandatory.
    */
    self::setPlugin(plugin: $plugin);

    /**
    * Registers the subcomponents of the current component.
    * View on ComponentTypes [COMMAND, LISTENER, SCHEDULER, NETWORK]
    */
    return [
      self::SCHEDULER_COMPONENT => [
        [
          'type' => 'repeating',
          'class' => new BossBarTask(),
          'sleep' => 20
        ],
        [
          'type' => 'repeating',
          'class' => new ScoreBoardTask(),
          'sleep' => 20
        ]
      ]
    ];
  }

}