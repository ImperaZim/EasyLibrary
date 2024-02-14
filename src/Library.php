<?php

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

use libraries\commando\PacketHooker;
use libraries\npcdialogue\NpcHooker;
use libraries\invmenu\InvMenuHandler;

/**
 * Class Library
 */
final class Library extends PluginBase {
  use SingletonTrait;

  /**
   * Called when the plugin is loaded.
   */
  public function onLoad() : void {
    self::setInstance($this);
  }

  /**
   * Called when the plugin is enabled.
   */
  public function onEnable() : void {
    new NpcHooker($this);
    new PacketHooker($this);
    new InvMenuHandler($this);
  }
}
