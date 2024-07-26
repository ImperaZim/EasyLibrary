<?php

declare(strict_types = 1);

use imperazim\components\world\WorldManager;
use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\traits\PluginToolkitTrait;

use imperazim\vendor\bossbar\BossBarManager;
use imperazim\vendor\invmenu\InvMenuManager;
use imperazim\vendor\commando\CommandoManager;
use imperazim\vendor\dialogue\DialogueManager;
use imperazim\vendor\customies\CustomiesManager;

/**
* Class Library
* TODO: This class should not be called in other plugins!
*/
final class Library extends PluginToolkit {
  use PluginToolkitTrait;

  private array $componentClasses = [
    'WorldManager' => WorldManager::class,
    'BossBarManager' => BossBarManager::class,
    'InvMenuManager' => InvMenuManager::class,
    'CommandoManager' => CommandoManager::class,
    'DialogueManager' => DialogueManager::class,
    'CustomiesManager' => CustomiesManager::class,
  ];

  /**
  * This method is called when the plugin is enabled.
  */
  protected function onEnable(): void {
    $this->saveRecursiveResources();
    $vendorComponents = $this->getConfig()->get('vendor', []);
    foreach ($vendorComponents as $componentName => $enable) {
      $this->validateComponentConfig($componentName, $enable);
      if ($enable) {
        $this->initializeComponent($componentName);
      }
    }
  }

  /**
  * Validates the configuration for a component.
  * @param string $componentName
  * @param mixed $enable
  * @throws \InvalidArgumentException
  */
  private function validateComponentConfig(string $componentName, $enable): void {
    if (!is_bool($enable)) {
      throw new \InvalidArgumentException("Invalid configuration for component '$componentName'. The value must be a boolean.");
    }
    if (!isset($this->componentClasses[$componentName]) && $enable) {
      throw new \RuntimeException("Unknown component configured: '$componentName'. Check the configuration and class mapping.");
    }
  }

  /**
  * Initializes a component if it is enabled and exists in the class mapping.
  * @param string $componentName
  */
  private function initializeComponent(string $componentName): void {
    $className = $this->componentClasses[$componentName];
    $this->addComponent($this, $className);
  }
}