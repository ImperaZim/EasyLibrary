<?php

declare(strict_types = 1);

use library\item\ItemFactory;
use library\world\WorldManager;
use library\plugin\PluginToolkit;
use pocketmine\utils\SingletonTrait;

use internal\bossbar\BossBarHooker;
use internal\invmenu\InvMenuHooker;
use internal\commando\CommandoHooker;
use internal\dialogue\DialogueHooker;

/**
* Class Library
* TODO: This class should not be called in other plugins!
*/
final class Library extends PluginToolkit {
  use SingletonTrait;

  /**
  * Called when the plugin is loaded.
  */
  protected function onLoad(): void {
    self::setInstance($this);
  }

  /**
  * Called when the plugin is enabled.
  */
  protected function onEnable(): void {
    $this->initHooks();
    $this->initComponents();
  }

  /**
  * Initialize hook handlers for various functionalities.
  */
  private function initHooks(): void {
    new BossBarHooker($this);
    new InvMenuHooker($this);
    new DialogueHooker($this);
    new CommandoHooker($this);
  }

  /**
  * Initialize components such as ItemFactory and WorldManager.
  */
  private function initComponents(): void {
    ItemFactory::init();
    WorldManager::init($this, $this->getServer()->getWorldManager());
  }

}