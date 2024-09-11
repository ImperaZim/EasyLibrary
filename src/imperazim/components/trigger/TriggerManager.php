<?php

declare(strict_types = 1);

namespace imperazim\components\trigger;

use pocketmine\scheduler\Task;

use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\PluginComponent;
use imperazim\components\plugin\traits\PluginComponentsTrait;

/**
* Class TriggerManager
* @package imperazim\components\trigger
*/
final class TriggerManager extends PluginComponent {
  use PluginComponentsTrait;

  /**
  * @var Trigger[] A list of registered Trigger instances stored statically for global access.
  */
  private static array $triggers = [];

  /**
  * Initializes the trigger component.
  * @param PluginToolkit $plugin The Plugin.
  */
  public static function init(PluginToolkit $plugin): array {
    self::setPlugin(plugin: $plugin);
    return [
      self::SCHEDULER_COMPONENT => [
        'type' => 'repeating',
        'class' => new class extends Task {
          public function onRun(): void {
            TriggerManager::runTriggers();
          }
        },
        'sleep' => 20
      ]
    ];
  }

  /**
  * Registers a new Trigger.
  * @param Trigger $trigger The trigger.
  */
  public static function addTrigger(Trigger $trigger): void {
    self::$triggers[] = $trigger;
  }

  /**
  * Runs all registered triggers, checking their conditions and executing actions if needed.
  */
  public static function runTriggers(): void {
    $onlinePlayers = self::getPlugin()->getServer()->getOnlinePlayers();

    $globalTriggers = [];
    $perPlayerTriggers = [];

    foreach (self::$triggers as $trigger) {
      if ($trigger->getTriggerType() === TriggerTypes::PER_PLAYER) {
        $perPlayerTriggers[] = $trigger;
      } elseif ($trigger->getTriggerType() === TriggerTypes::GLOBAL) {
        $globalTriggers[] = $trigger;
      }
    }

    foreach ($globalTriggers as $trigger) {
      $trigger->checkCondition();
    }

    if (!empty($onlinePlayers)) {
      foreach ($perPlayerTriggers as $trigger) {
        foreach ($onlinePlayers as $player) {
          $trigger->checkCondition($player);
        }
      }
    }
  }

}