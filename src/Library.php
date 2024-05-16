<?php

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

use world\WorldManager;
use libraries\commando\PacketHooker;
use libraries\npcdialogue\NpcHooker;
use libraries\invmenu\InvMenuHandler;

/**
 * Class Library
 */
final class Library extends PluginBase {
  use SingletonTrait;
  
  /** @var NpcHooker */
  public NpcHooker $dialogue;
  
  /** @var PacketHooker */
  public PacketHooker $commando;
  
  /** @var InvMenuHandler */
  public InvMenuHandler $invmenu;

  /**
   * Called when the plugin is loaded.
   */
  protected function onLoad() : void {
    self::setInstance($this);
  }

  /**
   * Called when the plugin is enabled.
   */
  protected function onEnable() : void {
    WorldManager::registerGenerators();
    $this->dialogue = new NpcHooker($this);
    $this->commando = new PacketHooker($this);
    $this->invmenu = new InvMenuHandler($this);
  }
}
