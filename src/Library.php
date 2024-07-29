<?php

declare(strict_types = 1);

use pocketmine\utils\TextFormat;

use imperazim\components\world\WorldManager;
use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\traits\PluginToolkitTrait;

use imperazim\vendor\bossbar\BossBarManager;
use imperazim\vendor\invmenu\InvMenuManager;
use imperazim\vendor\commando\CommandoManager;
use imperazim\vendor\dialogue\DialogueManager;
use imperazim\vendor\customies\CustomiesManager;
use imperazim\vendor\customies\enchantment\CustomiesEchantmentManager;

/**
* Class Library
* TODO: This class should not be called in other plugins!
*/
final class Library extends PluginToolkit {
  use PluginToolkitTrait;

  private array $componentClasses = [
    'World' => WorldManager::class,
    'BossBar' => BossBarManager::class,
    'InvMenu' => InvMenuManager::class,
    'Commando' => CommandoManager::class,
    'Dialogue' => DialogueManager::class,
    'Customies' => CustomiesManager::class,
    'CustomiesEnchantment' => CustomiesEchantmentManager::class,
  ];

  /**
  * This method is called when the plugin is enabled.
  */
  protected function onEnable(): void {
    $this->saveRecursiveResources();
    $logger = $this->getConfig()->get('logger', true);
    $vendorComponents = $this->getConfig()->get('vendor', []);
    foreach ($vendorComponents as $componentName => $enable) {
      $this->validateComponentConfig($componentName, $enable);
      if ($enable) {
        $className = $this->componentClasses[$componentName];
        $this->addComponent($this, $className);
      }
      if (is_bool($logger) && $logger) {
        $this->getServer()->getLogger()->notice(
          "§7$componentName component " . ($enable ? "§aON" : "§cOFF")
        );
      }
    }
  }

  /**
  * This method is called when the plugin is disabled.
  */
  protected function onDisable(): void {
    $customiesEnchantment = $this->getConfig()->get('vendor.CustomiesEnchantment', false);
    if ($customiesEnchantment) {
      CustomiesEchantmentManager::close();
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
}