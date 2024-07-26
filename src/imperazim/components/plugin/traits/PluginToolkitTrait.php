<?php

declare(strict_types = 1);

namespace imperazim\components\plugin\traits;

use imperazim\components\plugin\PluginToolkit;

/**
* Trait PluginToolkitTrait
* @package imperazim\components\plugin\traits
*/
trait PluginToolkitTrait {
  use ComponentTypesTrait;

  /** @var self|null Holds the singleton instance. */
  private static ?self $instance = null;

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

  /**
  * Creates a new instance of the class.
  * @return self A new instance of the class.
  */
  private static function createInstance() : self {
    return new self();
  }

  /**
  * Gets the singleton instance of the class.
  * @return self The singleton instance.
  */
  public static function getInstance() : self {
    if (self::$instance === null) {
      self::$instance = self::createInstance();
    }
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
  * Resets the singleton instance of the class to null.
  * @return void
  */
  public static function resetInstance() : void {
    self::$instance = null;
  }

  /**
  * Set the File instance with a token.
  * @param string $token
  * @param File $file
  */
  public static function setFile(string $token, File $file): void {
    self::$files[$token] = $file;
  }

  /**
  * Get the File instance.
  * If no token is provided and only one file exists, return that file.
  * @param string|null $token
  * @return File
  * @throws \RuntimeException If the token is not provided and there are multiple files.
  */
  public static function getFile(?string $token = null): File {
    if ($token !== null) {
      if (isset(self::$files[$token])) {
        return self::$files[$token];
      } else {
        throw new \RuntimeException("No file found for token: $token");
      }
    }

    if (count(self::$files) === 1) {
      return reset(self::$files);
    }

    throw new \RuntimeException("Token must be provided when multiple files are set.");
  }

  /**
  * Set the PluginToolkit or PluginBase instance.
  * @param PluginToolkit|PluginBase $plugin
  */
  public static function setPlugin(PluginToolkit $plugin): void {
    self::$plugin = $plugin;
  }

  /**
  * Get the PluginToolkit instance.
  * @return PluginToolkit
  */
  public static function getPlugin(): PluginToolkit {
    return self::$plugin;
  }

  /**
  * Call a method on the plugin instance.
  * @param string $method
  * @param array $args
  * @return mixed
  * @throws \BadMethodCallException If the method does not exist on the plugin instance.
  */
  public static function callPluginMethod(string $method, array $args = []) {
    if (method_exists(self::$plugin, $method)) {
      return self::$plugin->$method(...$args);
    }

    throw new \BadMethodCallException("Method $method does not exist on the plugin instance.");
  }
}