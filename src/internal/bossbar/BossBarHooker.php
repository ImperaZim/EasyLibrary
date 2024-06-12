<?php

declare(strict_types = 1);

namespace internal\bossbar;

use InvalidArgumentException;
use pocketmine\plugin\Plugin;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\BossEventPacket;

/**
* Class BossBarHooker
* @package internal\bossbar
*/
final class BossBarHooker implements Listener {

  /**
  * BossBarHooker constructor.
  * @param PluginBase|null $registrant
  */
  public function __construct(private ?PluginBase $registrant = null) {
    $this->register();
  }

  /**
  * Checks if the hooker is registered.
  * @return bool
  */
  public function isRegistered(): bool {
    return $this->registrant instanceof Plugin;
  }

  /**
  * Gets the registrant plugin.
  * @return Plugin
  */
  public function getRegistrant(): Plugin {
    return $this->registrant;
  }

  /**
  * Unregisters the hooker.
  * @return void
  */
  public function unregister(): void {
    $this->registrant = null;
  }

  /**
  * Registers the hooker.
  * @return void
  */
  public function register(): void {
    if ($this->isRegistered()) {
      return;
    }
    $this->registrant->getServer()->getPluginManager()->registerEvents($this, $this->registrant);
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
        $this->registrant->getServer()->getLogger()->debug("Got BossEventPacket " . ($pk->eventType === BossEventPacket::TYPE_REGISTER_PLAYER ? "" : "un") . "register by client for player id " . $pk->playerActorUniqueId);
        break;
      default:
        $e->getOrigin()->getPlayer()->kick("Invalid packet received", false);
      }
    }
  }