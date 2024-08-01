<?php

declare(strict_types = 1);

namespace imperazim\vendor\customies\enchantment\tasks;

use imperazim\vendor\customies\enchantment\utils\Utils;
use imperazim\vendor\customies\enchantment\enchants\CustomEnchant;
use imperazim\vendor\customies\enchantment\enchants\TickingEnchantment;

use pocketmine\Server;
use pocketmine\item\Item;
use pocketmine\scheduler\Task;
use pocketmine\item\ItemTypeIds;
use pocketmine\utils\TextFormat;

/**
* Class TickEnchantmentsTask
* @package imperazim\vendor\customies\enchantment\tasks
*/
class TickEnchantmentsTask extends Task {

  /**
  * Execute the task.
  * @return void
  */
  public function onRun(): void {
    $server = Server::getInstance();
    $currentTick = $server->getTick();
    foreach ($server->getOnlinePlayers() as $player) {
      $successfulEnchantments = [];
      foreach ($player->getInventory()->getContents() as $slot => $content) {
        if ($content->getNamedTag()->getTag("ENCHANTMENT_CORE") === null && count($content->getEnchantments()) > 0) {
          $player->getInventory()->setItem($slot, $this->cleanOldItems($content));
        }
        foreach ($content->getEnchantments() as $enchantmentInstance) {
          /** @var TickingEnchantment $enchantment */
          $enchantment = $enchantmentInstance->getType();
          if ($enchantment instanceof CustomEnchant && $enchantment->canTick()) {
            if (!in_array($enchantment, $successfulEnchantments, true) || $enchantment->supportsMultipleItems()) {
              if ($this->isValidEnchantmentUsage($enchantment, $slot, $player)) {
                if ($currentTick % $enchantment->getTickingInterval() === 0) {
                  $enchantment->onTick($player, $content, $player->getInventory(), $slot, $enchantmentInstance->getLevel());
                  $successfulEnchantments[] = $enchantment;
                }
              }
            }
          }
        }
      }
      foreach ($player->getArmorInventory()->getContents() as $slot => $content) {
        if ($content->getNamedTag()->getTag("ENCHANTMENT_CORE") === null && count($content->getEnchantments()) > 0) {
          $player->getArmorInventory()->setItem($slot, $this->cleanOldItems($content));
        }
        foreach ($content->getEnchantments() as $enchantmentInstance) {
          /** @var TickingEnchantment $enchantment */
          $enchantment = $enchantmentInstance->getType();
          if ($enchantment instanceof CustomEnchant && $enchantment->canTick()) {
            if (!in_array($enchantment, $successfulEnchantments, true) || $enchantment->supportsMultipleItems()) {
              if ($this->isValidEnchantmentUsage($enchantment, $slot, $player)) {
                if ($currentTick % $enchantment->getTickingInterval() === 0) {
                  $enchantment->onTick($player, $content, $player->getArmorInventory(), $slot, $enchantmentInstance->getLevel());
                  $successfulEnchantments[] = $enchantment;
                }
              }
            }
          }
        }
      }
    }
  }

  /**
  * Clean old items by removing outdated enchantments.
  * @param Item $item
  * @return Item
  */
  public function cleanOldItems(Item $item): Item {
    foreach ($item->getEnchantments() as $enchantmentInstance) {
      $enchantment = $enchantmentInstance->getType();
      if ($enchantment instanceof CustomEnchant) {
        $item->setCustomName(str_replace(
          "\n" . Utils::getColorFromRarity($enchantment->getRarity()) . $enchantment->name . " " . Utils::getRomanNumeral($enchantmentInstance->getLevel()),
          "",
          $item->getCustomName()
        ));
        $lore = $item->getLore();
        if (($key = array_search(
          Utils::getColorFromRarity($enchantment->getRarity()) . $enchantment->name . " " . Utils::getRomanNumeral($enchantmentInstance->getLevel()),
          $lore,
          true
        )) !== false) {
          unset($lore[$key]);
        }
        $item->setLore($lore);
      }
    }
    $item->getNamedTag()->setInt("ENCHANTMENT_CORE", 0);
    return $item;
  }

  /**
  * Check if an enchantment is valid for use.
  * @param CustomEnchant $enchantment
  * @param int $slot
  * @param Player $player
  * @return bool
  */
  private function isValidEnchantmentUsage(CustomEnchant $enchantment, int $slot, Player $player): bool {
    return match ($enchantment->getUsageType()) {
      CustomEnchant::TYPE_ANY_INVENTORY,
      CustomEnchant::TYPE_INVENTORY,
      CustomEnchant::TYPE_HAND => $slot === $player->getInventory()->getHeldItemIndex(),
      CustomEnchant::TYPE_ARMOR_INVENTORY,
      CustomEnchant::TYPE_HELMET => Utils::isHelmet($content),
      CustomEnchant::TYPE_CHESTPLATE => Utils::isChestplate($content),
      CustomEnchant::TYPE_LEGGINGS => Utils::isLeggings($content),
      CustomEnchant::TYPE_BOOTS => Utils::isBoots($content),
      default => false,
      };
    }
  }