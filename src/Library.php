<?php

declare(strict_types = 1);

use library\item\ItemFactory;
use library\world\WorldManager;
use library\plugin\PluginToolkit;

use pocketmine\utils\SingletonTrait;

use internal\invmenu\InvMenuHandler;
use internal\bossbarr\BossBarHooker;
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
  protected function onEnable() : void {
    new BossBarHooker($this);
    new InvMenuHandler($this);
    new DialogueHooker($this);
    new CommandoHooker($this);
    
    ItemFactory::init($this->getServer()->getAsyncPool());
    WorldManager::init($this, $this->getServer()->getWorldManager());
  }
}