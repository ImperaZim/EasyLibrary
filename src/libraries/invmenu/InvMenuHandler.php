<?php

declare(strict_types = 1);

namespace libraries\invmenu;

use LogicException;
use pocketmine\Server;
use InvalidArgumentException;
use pocketmine\plugin\PluginBase;
use libraries\invmenu\session\PlayerManager;
use libraries\invmenu\type\InvMenuTypeRegistry;

final class InvMenuHandler {

  private static ?PluginBase $registrant = null;
  private static InvMenuTypeRegistry $type_registry;
  private static PlayerManager $player_manager;

  public function __construct(PluginBase $plugin) {
    self::register($plugin);
  }

  public static function register(PluginBase $plugin) : void {
    if (!self::isRegistered()) {
      self::$registrant = $plugin;
      self::$type_registry = new InvMenuTypeRegistry();
      self::$player_manager = new PlayerManager(self::getRegistrant());
      Server::getInstance()->getPluginManager()->registerEvents(new InvMenuEventHandler(self::getPlayerManager()), $plugin);
    }
  }

  public static function isRegistered() : bool {
    return self::$registrant instanceof PluginBase;
  }

  public static function getRegistrant() : PluginBase {
    return self::$registrant ?? throw new LogicException("Cannot obtain registrant before registration");
  }

  public static function getTypeRegistry() : InvMenuTypeRegistry {
    return self::$type_registry;
  }

  public static function getPlayerManager() : PlayerManager {
    return self::$player_manager;
  }
}