<?php

declare(strict_types = 1);

namespace libraries\npcdialogue;

use pocketmine\plugin\PluginBase;

/**
* Class NpcHooker
* @package libraries\npcdialogue
*/
final class NpcHooker {

  /**
  * @var \Plugin|null
  */
  private static ?PluginBase $plugin = null;

  /**
  * NpcHooker constructor.
  * @param \Plugin $plugin
  */
  public function __construct(PluginBase $plugin) {
    if (!self::isRegistered()) {
      self::register($plugin);
    }
  }

  /**
  * Checks if the plugin is registered.
  * @return bool
  */
  public static function isRegistered() : bool {
    return self::$plugin !== null && self::$plugin->isEnabled();
  }

  /**
  * Registers the plugin.
  * @param \Plugin $plugin
  * @throws \RuntimeException
  */
  public static function register(PluginBase $plugin) : void {
    if (self::$plugin !== null) {
      throw new \RuntimeException("Plugin is already registered");
    }
    self::$plugin = $plugin;
    self::$plugin->getServer()->getPluginManager()->registerEvents(new PacketHandler(), self::$plugin);
  }
}