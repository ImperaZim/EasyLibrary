<?php

declare(strict_types = 1);

namespace imperazim\vendor\bossbar;

use InvalidArgumentException;
use pocketmine\plugin\Plugin;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\BossEventPacket;

use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\PluginComponent;
use imperazim\components\plugin\traits\PluginComponentsTrait;

/**
* Class BossBarManager
* @package imperazim\vendor\bossbar
*/
final class BossBarManager extends PluginComponent implements Listener {
  use PluginComponentsTrait;

  /**
  * Initializes the bossbar component.
  * @param PluginToolkit $plugin The Plugin.
  */
  public static function init(PluginToolkit $plugin): array {
    self::setPlugin(plugin: $plugin);
    return [
      self::LISTENER_COMPONENT => [
        new self()
      ]
    ];
  }

  /**
  * Handles data packet receive event.
  * @param DataPacketReceiveEvent $e
  * @return void
  */
  public function onDataPacketReceiveEvent(DataPacketReceiveEvent $e): void {
    if ($e->getPacket() instanceof BossEventPacket) {
      $this->onBossEventPacket($e);
    }
  }

  /**
  * Handles boss event packet.
  * @param DataPacketReceiveEvent $e
  * @return void
  */
  private function onBossEventPacket(DataPacketReceiveEvent $e): void {
    if (!($pk = $e->getPacket()) instanceof BossEventPacket) {
      throw new InvalidArgumentException(get_class($e->getPacket()) . " is not a " . BossEventPacket::class);
    }
    switch ($pk->eventType) {
      case BossEventPacket::TYPE_REGISTER_PLAYER:
      case BossEventPacket::TYPE_UNREGISTER_PLAYER:
        self::getPlugin()->getServer()->getLogger()->debug("Got BossEventPacket " . ($pk->eventType === BossEventPacket::TYPE_REGISTER_PLAYER ? "" : "un") . "register by client for player id " . $pk->playerActorUniqueId);
        break;
      default:
        $e->getOrigin()->getPlayer()->kick("Invalid packet received", false);
      }
    }
  }