<?php

declare(strict_types = 1);

use imperazim\components\world\WorldManager;
use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\traits\PluginToolkit;

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

  /**
  * This method is called when the plugin is enabled.
  */
  protected function onEnable(): void {
    $this->addComponent($this, WorldManager::class);
    $this->addComponent($this, BossBarManager::class);
    $this->addComponent($this, InvMenuManager::class);
    $this->addComponent($this, CommandoManager::class);
    $this->addComponent($this, DialogueManager::class);
    $this->addComponent($this, CustomiesManager::class);
  }
}