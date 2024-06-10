<?php

declare(strict_types = 1);

namespace internal\dialogue\player;

use pocketmine\event\Listener;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\RequestPacket;
use pocketmine\network\mcpe\protocol\UpdateAbilitiesPacket;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use PrefixedLogger;
use RuntimeException;
use function array_diff_key;
use function array_map;
use function count;

/**
* Class PlayerManager
* @package internal\dialogue\player
*/
final class PlayerManager implements Listener {

  /** @var array<int, PlayerInstance> */
  private array $players = [];

  /** @var array<int, int> */
  private array $ticking = [];

  /**
  * PlayerManager Constructor.
  * @param Plugin|null $plugin Plugin registrant.
  */
  public function __construct(private ?Plugin $plugin = null) {}

  public function onPlayerLogin(PlayerLoginEvent $event): void {
    // var_dump($event->getEventName());
    $player = $event->getPlayer();
    $this->players[$player->getId()] = new PlayerInstance($this, $player, new PrefixedLogger($this->plugin->getLogger(), $player->getName()));
  }

  public function onPlayerQuit(PlayerQuitEvent $event): void {
    // var_dump($event->getEventName());
    $player = $event->getPlayer();
    $id = $player->getId();
    if (isset($this->players[$id])) {
      $this->players[$id]->destroy();
      unset($this->players[$id], $this->ticking[$id]);
    }
  }

  public function onTick(): void {
    foreach ($this->ticking as $id) {
      if (!$this->players[$id]->tick()) {
        unset($this->ticking[$id]);
      }
    }
  }

  public function onDataPacketReceive(DataPacketReceiveEvent $event): void {
    var_dump('Call DataPacketReceiveEvent');
    $packet = $event->getPacket();
    if (!($packet instanceof RequestPacket)) {
      var_dump('Error (DataPacketReceiveEvent::$event) is not a RequestPacket');
      return;
    }
    $player = $event->getOrigin()->getPlayer();
    if ($player === null) {
      var_dump('Error (DataPacketReceiveEvent::getOrigin()::getPlayer()) is null');
      return;
    }
    $instance = $this->getPlayerNullable($player);
    if ($instance === null) {
      var_dump('Error (PlayerManager::getPlayerNullable()) is null');
      return;
    }
    var_dump($packet->requestType);
    if ($packet->requestType === RequestPacket::REQUEST_EXECUTE_ACTION) { 
      $instance->onDialogueRespond($packet->sceneName, $packet->actionIndex);
    } elseif ($packet->requestType === RequestPacket::REQUEST_EXECUTE_OPENING_COMMANDS) {
      $instance->onDialogueReceive();
    } elseif ($packet->requestType === RequestPacket::REQUEST_EXECUTE_CLOSING_COMMANDS) {
      $instance->onDialogueClose();
    }
  }

  public function onDataPacketSend(DataPacketSendEvent $event): void {
    var_dump('Call DataPacketSendEvent');
    static $processing = false;
    if ($processing) {
      return;
    }

    $packets = $event->getPackets();
    $targets = $event->getTargets();
    $remove = [];
    foreach ($packets as $packet) {
      if (!($packet instanceof UpdateAbilitiesPacket)) {
        continue;
      }
      foreach ($targets as $id => $target) {
        $player = $target->getPlayer();
        if ($player === null) {
          continue;
        }

        $instance = $this->getPlayerNullable($player);
        if ($instance === null) {
          continue;
        }

        $replacement = $instance->handleUpdateAbilities($packet);
        if ($replacement === null) {
          continue;
        }

        $processing = true;
        $target->sendDataPacket($replacement);
        $processing = false;
        $remove[$id] = null;
      }
    }

    if (count($remove) === 0) {
      return;
    }

    $event->cancel();

    $new_targets = array_diff_key($targets, $remove);
    if (count($new_targets) > 0) {
      $processing = false;
      NetworkBroadcastUtils::broadcastPackets(array_map(
        static fn(NetworkSession $session) : Player => $session->getPlayer() ?? throw new RuntimeException("Expected connected player"),
        $new_targets
      ), $packets);
      $processing = true;
    }
  }

  /**
  * Gets the player instance.
  * @param Player $player The player instance.
  * @return PlayerInstance The corresponding player instance.
  */
  public function getPlayer(Player $player) : PlayerInstance {
    if (!isset($this->players[$player->getId()])) {
      $this->players[$player->getId()] = new PlayerInstance($this, $player, new PrefixedLogger($this->plugin->getLogger(), $player->getName()));
    }
    isset($this->players[$player->getId()]) || var_dump('Call getPlayer');
    return $this->players[$player->getId()];
  }

  /**
  * Gets the player instance or null if not found.
  * @param Player $player The player instance.
  * @return PlayerInstance|null The corresponding player instance or null.
  */
  public function getPlayerNullable(Player $player) : ?PlayerInstance {
    if (!isset($this->players[$player->getId()])) {
      $this->players[$player->getId()] = new PlayerInstance($this, $player, new PrefixedLogger($this->plugin->getLogger(), $player->getName()));
    }
    isset($this->players[$player->getId()]) || var_dump('Call getPlayerNullable');
    return $this->players[$player->getId()] ?? null;
  }

  /**
  * Marks the player instance for ticking.
  * @param Player $player The player instance.
  */
  public function tick(Player $player) : void {
    if (isset($this->players[$id = $player->getId()])) {
      $this->ticking[$id] = $id;
    }
  }

  /**
  * Unmarks the player instance from ticking.
  * @param Player $player The player instance.
  */
  public function unTick(Player $player) : void {
    unset($this->ticking[$player->getId()]);
  }
}