<?php

declare(strict_types = 1);

namespace internal\customitem;

use RuntimeException;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskHandler;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\Experiments;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\BiomeDefinitionListPacket;
use function mkdir;
use function is_dir;
use function sprintf;
use function class_exists;

/**
* Class CustomItemHooker
* @package internal\customitem
*/
final class CustomItemHooker implements Listener {

  /** @var TaskHandler[][] */
  protected array $handlers = [];

  /**
  * CustomItemHooker constructor.
  * @param PluginBase|null $registrant Plugin registrant.
  */
  public function __construct(private ?PluginBase $registrant = null) {
    $registrant->saveDefaultConfig();

    if (!is_dir($registrant->getResourcePackFolder()) && !mkdir($concurrentDirectory = $registrant->getResourcePackFolder()) && !is_dir($concurrentDirectory)) {
      throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
    }

    try {
      CustomItemManager::getInstance()->registerDefaultItems($registrant->getConfig()->get("items", []));
    }catch(\Throwable $e) {
      $registrant->getLogger()->critical("Failed to load custom items: " . $e->getMessage() . ", disabling plugin to prevent any unintended behaviour...");
      $registrant->getLogger()->logException($e);
      $registrant->getServer()->getPluginManager()->disablePlugin($registrant);
      return;
    }

    $registrant->getServer()->getPluginManager()->registerEvents($this, $registrant);
  }

  public function onDataPacketSend(DataPacketSendEvent $event) : void {
    $packets = $event->getPackets();
    foreach ($packets as $packet) {
      if ($packet instanceof StartGamePacket) {
        $packet->levelSettings->experiments = new Experiments([
          "data_driven_items" => true
        ], true);
      } elseif ($packet instanceof ResourcePackStackPacket) {
        $packet->experiments = new Experiments([
          "data_driven_items" => true
        ], true);
      } elseif ($packet instanceof BiomeDefinitionListPacket) {
        foreach ($event->getTargets() as $session) {
          $session->sendDataPacket(CustomItemManager::getInstance()->getPacket());
        }
      }
    }
  }

  public function onPlayerQuit(PlayerQuitEvent $event) : void {
    $player = $event->getPlayer();
    if (!isset($this->handlers[$player->getName()])) {
      return;
    }
    foreach ($this->handlers[$player->getName()] as $blockHash => $handler) {
      $handler->cancel();
    }
    unset($this->handlers[$player->getName()]);
  }
}