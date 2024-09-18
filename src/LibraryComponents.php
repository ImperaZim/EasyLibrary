<?php

declare(strict_types = 1);

use imperazim\bugfixes\BugFixesManager;

use imperazim\components\filesystem\File;
use imperazim\components\world\WorldManager;
use imperazim\components\plugin\PluginToolkit;
use imperazim\components\trigger\TriggerManager;
use imperazim\components\command\CommandManager;
use imperazim\components\hud\bossbar\BossBarManager;

use imperazim\vendor\invmenu\InvMenuManager;
use imperazim\vendor\commando\CommandoManager;
use imperazim\vendor\dialogue\DialogueManager;
use imperazim\vendor\customies\CustomiesManager;
use imperazim\vendor\customies\enchantment\CustomiesEchantmentManager;

/**
* Class LibraryComponents
* TODO: This class should not be called in other plugins!
*/
final class LibraryComponents {

  /** @var array */
  private array $componentClasses = [
    'World' => WorldManager::class,
    'BossBar' => BossBarManager::class,
    'InvMenu' => InvMenuManager::class,
    'Command' => CommandManager::class,
    'Triggers' => TriggerManager::class,
    'BugFixes' => BugFixesManager::class,
    'Commando' => CommandoManager::class,
    'Dialogue' => DialogueManager::class,
    'Customies' => CustomiesManager::class,
    'CustomiesEnchantment' => CustomiesEchantmentManager::class,
  ];

  /** @var array */
  public array $enabledComponents = [];

  /** @var File|null */
  public ?File $componentsFile;

  /**
  * LibraryComponents constructor.
  */
  public function __construct(private PluginToolkit $plugin) {
    $this->componentsFile = new File(
      directoryOrConfig: $plugin->data,
      fileName: "components",
      fileType: File::TYPE_YML,
      autoGenerate: true,
      readCommand: [
        "--merge" => [
          "logger" => true,
          "vendor" => []
        ]
      ]
    );
    foreach ($this->componentClasses as $name => $class) {
      if ($this->componentsFile->get("vendor.$name") === null) {
        $this->componentsFile->set(["vendor.$name" => true]);
      }
    }

  }

  /**
  * Enable components
  */
  public function enableComponents(): void {
    $logger = $this->componentsFile->get("logger", true);
    $vendorComponents = $this->componentsFile->get("vendor", []);
    foreach ($vendorComponents as $componentName => $enable) {
      $this->validateComponentConfig($componentName, $enable);
      if ($enable) {
        $className = $this->componentClasses[$componentName];
        $this->plugin->addComponent($this->plugin, $className);
        $this->enabledComponents[] = $className;
      }
      if ($logger) {
        $this->plugin->getServer()->getLogger()->notice(
          "§7$componentName component " . ($enable ? "§aON" : "§cOFF")
        );
      }
    }
  }

  /**
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
  * Disable Customies [Enchantments]
  */
  public function disableCustomiesEnchantment(bool $customiesEnchantment): void {
    if ($customiesEnchantment) {
      CustomiesEchantmentManager::close();
    }
  }
}