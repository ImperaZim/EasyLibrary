<?php

declare(strict_types = 1);

namespace imperazim\components\plugin\traits;

use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\PluginComponent;

/**
* Trait PluginToolkitTrait
* @package imperazim\components\plugin\traits
*/
trait PluginToolkitTrait {
  use ComponentTypesTrait;

  /** @var self|null */
  private static $instance = null;

  /**
  * Gets the singleton instance of the class.
  * @return self The singleton instance.
  */
  public static function getInstance() : self {
    return self::$instance;
  }

  /**
  * Sets the singleton instance of the class.
  * @param self $instance The instance to set.
  * @return void
  */
  public static function setInstance(self $instance) : void {
    self::$instance = $instance;
  }

  /**
  * Processes the initialization of a PluginComponent.
  *
  * @param PluginToolkit $plugin The Plugin.
  * @param string $component The PluginComponent class name.
  * @throws \InvalidArgumentException
  */
  public function addComponent(PluginToolkit $plugin, string $component): void {
    if (is_subclass_of($component, PluginComponent::class)) {
      $componentInstance = $component::init($plugin);

      $commandC = self::COMMAND_COMPONENT;
      $listenerC = self::LISTENER_COMPONENT;
      $schedulerC = self::SCHEDULER_COMPONENT;

      if (isset($componentInstance[$commandC])) {
        $plugin->initComponents(
          type: $commandC,
          components: $componentInstance[$commandC]
        );
      }

      if (isset($componentInstance[$listenerC])) {
        $plugin->initComponents(
          type: $listenerC,
          components: $componentInstance[$listenerC]
        );
      }

      if (isset($componentInstance[$schedulerC])) {
        $scheduler = $plugin->getScheduler();
        $tasks = $componentInstance[$schedulerC];
        if (isset($tasks['type'])) {
          $tasks = [$tasks];
        }
        foreach ($tasks as $taskConfig) {
          switch ($taskConfig['type']) {
            case 'repeating':
              $scheduler->scheduleRepeatingTask(
                $taskConfig['class'],
                $taskConfig['sleep']
              );
              break;
            case 'delayed':
              $scheduler->scheduleDelayedTask(
                $taskConfig['class'],
                $taskConfig['sleep']
              );
              break;
            default:
              // null...
              break;
          }
        }
      }
    } else {
      throw new \InvalidArgumentException("Class $component must be a subclass of " . PluginComponent::class);
    }
  }

}