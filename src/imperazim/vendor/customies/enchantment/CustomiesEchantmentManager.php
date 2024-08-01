<?php

declare(strict_types = 1);

namespace imperazim\vendor\customies\enchantment;

use pocketmine\block\BlockTypeIds;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityEffectAddEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\inventory\ArmorInventory;
use pocketmine\inventory\CallbackInventoryListener;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\PlayerInventory;
use pocketmine\player\Player;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\InventoryContentPacket;
use pocketmine\network\mcpe\protocol\InventorySlotPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;

use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\PluginComponent;
use imperazim\components\plugin\traits\PluginComponentsTrait;

use imperazim\vendor\customies\enchantment\enchants\ReactiveEnchantment;
use imperazim\vendor\customies\enchantment\enchants\ToggleableEnchantment;
use imperazim\vendor\customies\enchantment\tasks\TickEnchantmentsTask;
use imperazim\vendor\customies\enchantment\utils\Utils;

/**
* Class CustomiesEchantmentManager
* @package imperazim\vendor\customies\enchantment
*/
final class CustomiesEchantmentManager extends PluginComponent implements Listener {
  use PluginComponentsTrait;

  /**
  * Initializes the customies enchantment component.
  * @param PluginToolkit $plugin The Plugin.
  */
  public static function init(PluginToolkit $plugin): array {
    self::setPlugin($plugin);
    return [
      self::LISTENER_COMPONENT => [
        new self()
      ],
      self::SCHEDULER_COMPONENT => [
        'type' => 'repeating',
        'class' => new TickEnchantmentsTask(),
        'sleep' => 1
      ],
    ];
  }

  /**
  * Disable the customies enchantment component.
  */
  public static function close(): void {
    $plugin = self::getPlugin();
    foreach ($plugin->getServer()->getOnlinePlayers() as $player) {
      self::toggleEnchantmentsForInventory($player, $player->getInventory(), false);
      self::toggleEnchantmentsForInventory($player, $player->getArmorInventory(), false);
    }
  }

  private static function toggleEnchantmentsForInventory(Player $player, Inventory $inventory, bool $enabled): void {
    foreach ($inventory->getContents() as $slot => $content) {
      foreach ($content->getEnchantments() as $enchantmentInstance) {
        ToggleableEnchantment::attemptToggle($player, $content, $enchantmentInstance, $inventory, $slot, $enabled);
      }
    }
  }

  /**
  * BlockBreakEvent handler
  * @priority HIGHEST
  */
  public function onBreak(BlockBreakEvent $event): void {
    ReactiveEnchantment::attemptReaction($event->getPlayer(), $event);
  }

  /**
  * DataPacketReceiveEvent handler
  */
  public function onDataPacketReceive(DataPacketReceiveEvent $event): void {
    $packet = $event->getPacket();
    if ($packet instanceof InventoryTransactionPacket) {
      foreach ($packet->trData->getActions() as $action) {
        $action->oldItem = new ItemStackWrapper($action->oldItem->getStackId(), Utils::filterDisplayedEnchants($action->oldItem->getItemStack()));
        $action->newItem = new ItemStackWrapper($action->newItem->getStackId(), Utils::filterDisplayedEnchants($action->newItem->getItemStack()));
      }
    }
    if ($packet instanceof MobEquipmentPacket) {
      Utils::filterDisplayedEnchants($packet->item->getItemStack());
    }
  }

  /**
  * DataPacketSendEvent handler
  */
  public function onDataPacketSend(DataPacketSendEvent $event): void {
    foreach ($event->getPackets() as $packet) {
      if ($packet instanceof InventorySlotPacket) {
        $packet->item = new ItemStackWrapper($packet->item->getStackId(), Utils::displayEnchants($packet->item->getItemStack()));
      }
      if ($packet instanceof InventoryContentPacket) {
        foreach ($packet->items as $i => $item) {
          $packet->items[$i] = new ItemStackWrapper($item->getStackId(), Utils::displayEnchants($item->getItemStack()));
        }
      }
    }
  }

  /**
  * EntityDamageEvent handler
  * @priority HIGHEST
  */
  public function onDamage(EntityDamageEvent $event): void {
    $entity = $event->getEntity();
    if ($entity instanceof Player) {
      if ($event->getCause() === EntityDamageEvent::CAUSE_FALL && !Utils::shouldTakeFallDamage($entity)) {
        $event->cancel();
        return;
      }
      ReactiveEnchantment::attemptReaction($entity, $event);
    }
    if ($event instanceof EntityDamageByEntityEvent) {
      $attacker = $event->getDamager();
      if ($attacker instanceof Player) {
        ReactiveEnchantment::attemptReaction($attacker, $event);
      }
    }
  }

  /**
  * EntityEffectAddEvent handler
  * @priority HIGHEST
  */
  public function onEffectAdd(EntityEffectAddEvent $event): void {
    $entity = $event->getEntity();
    if ($entity instanceof Player) {
      ReactiveEnchantment::attemptReaction($entity, $event);
    }
  }

  /**
  * EntityShootBowEvent handler
  * @priority HIGHEST
  */
  public function onShootBow(EntityShootBowEvent $event): void {
    $entity = $event->getEntity();
    if ($entity instanceof Player) {
      ReactiveEnchantment::attemptReaction($entity, $event);
    }
  }

  /**
  * PlayerDeathEvent handler
  */
  public function onDeath(PlayerDeathEvent $event): void {
    ReactiveEnchantment::attemptReaction($event->getPlayer(), $event);
  }

  /**
  * PlayerInteractEvent handler
  * @priority HIGHEST
  */
  public function onInteract(PlayerInteractEvent $event): void {
    ReactiveEnchantment::attemptReaction($event->getPlayer(), $event);
  }

  /**
  * PlayerItemHeldEvent handler
  * @priority HIGHEST
  */
  public function onItemHold(PlayerItemHeldEvent $event): void {
    $player = $event->getPlayer();
    $inventory = $player->getInventory();
    $oldItem = $inventory->getItemInHand();
    $newItem = $event->getItem();

    $this->toggleEnchantments($player, $inventory, $oldItem, false);
    $this->toggleEnchantments($player, $inventory, $newItem, true);
  }

  private function toggleEnchantments(Player $player, Inventory $inventory, Item $item, bool $enabled): void {
    foreach ($item->getEnchantments() as $enchantmentInstance) {
      ToggleableEnchantment::attemptToggle($player, $item, $enchantmentInstance, $inventory, $inventory->getHeldItemIndex(), $enabled);
    }
  }

  /**
  * PlayerJoinEvent handler
  */
  public function onJoin(PlayerJoinEvent $event): void {
    $player = $event->getPlayer();

    self::toggleEnchantmentsForInventory($player, $player->getInventory(), true);
    self::toggleEnchantmentsForInventory($player, $player->getArmorInventory(), true);

    $this->addInventoryListeners($player);
  }

  private function addInventoryListeners(Player $player): void {
    $onSlot = function (Inventory $inventory, int $slot, Item $oldItem): void {
      if ($inventory instanceof PlayerInventory || $inventory instanceof ArmorInventory) {
        $holder = $inventory->getHolder();
        if ($holder instanceof Player) {
          if (!$oldItem->equals(($newItem = $inventory->getItem($slot)), false)) {
            foreach ($oldItem->getEnchantments() as $oldEnchantment) {
              ToggleableEnchantment::attemptToggle($holder, $oldItem, $oldEnchantment, $inventory, $slot, false);
            }
            foreach ($newItem->getEnchantments() as $newEnchantment) {
              ToggleableEnchantment::attemptToggle($holder, $newItem, $newEnchantment, $inventory, $slot);
            }
          }
        }
      }
    };

    $onContent = function (Inventory $inventory, array $oldContents) use ($onSlot): void {
      foreach ($oldContents as $slot => $oldItem) {
        if (!($oldItem ?? VanillaItems::AIR())->equals($inventory->getItem($slot), !$inventory instanceof ArmorInventory)) {
          $onSlot($inventory, $slot, $oldItem);
        }
      }
    };

    $player->getInventory()->getListeners()->add(new CallbackInventoryListener($onSlot, $onContent));
    $player->getArmorInventory()->getListeners()->add(new CallbackInventoryListener($onSlot, $onContent));
  }

  /**
  * PlayerMoveEvent handler
  * @priority HIGHEST
  */
  public function onMove(PlayerMoveEvent $event): void {
    $player = $event->getPlayer();
    if (!Utils::shouldTakeFallDamage($player)) {
      if ($player->getWorld()->getBlock($player->getPosition()->floor()->subtract(0, 1, 0))->getTypeId() !== BlockTypeIds::AIR && Utils::getNoFallDamageDuration($player) <= 0) {
        Utils::setShouldTakeFallDamage($player, true);
      } else {
        Utils::increaseNoFallDamageDuration($player);
      }
    }
    if ($event->getFrom()->floor()->equals($event->getTo()->floor())) return;
    ReactiveEnchantment::attemptReaction($player, $event);
  }

  /**
  * PlayerQuitEvent handler
  * @priority HIGHEST
  */
  public function onQuit(PlayerQuitEvent $event): void {
    $player = $event->getPlayer();
    if (!$player->isClosed()) {
      self::toggleEnchantmentsForInventory($player, $player->getInventory(), false);
      self::toggleEnchantmentsForInventory($player, $player->getArmorInventory(), false);
    }
  }

  /**
  * PlayerToggleSneakEvent handler
  * @priority HIGHEST
  */
  public function onSneak(PlayerToggleSneakEvent $event): void {
    ReactiveEnchantment::attemptReaction($event->getPlayer(), $event);
  }

  /**
  * ProjectileHitBlockEvent handler
  * @priority HIGHEST
  */
  public function onProjectileHitBlock(ProjectileHitBlockEvent $event): void {
    $shooter = $event->getEntity()->getOwningEntity();
    if ($shooter instanceof Player) {
      ReactiveEnchantment::attemptReaction($shooter, $event);
    }
  }

  /**
  * ProjectileLaunchEvent handler
  * @priority HIGHEST
  */
  public function onProjectileLaunch(ProjectileLaunchEvent $event): void {
    $projectile = $event->getEntity();
    $shooter = $projectile->getOwningEntity();
    if (($shooter) instanceof Player) {
      ProjectileTracker::addProjectile($projectile, $shooter->getInventory()->getItemInHand());
    }
  }
}