<?php

declare(strict_types = 1);

namespace ImperaZim\menus;

use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use pocketmine\inventory\Inventory;

use library\interface\Menu;
use ImperaZim\PluginExample;
use internal\bedrock\ItemFactory;
use internal\invmenu\InvMenu as IMenu;
use internal\invmenu\types\InvMenuChest;
use internal\invmenu\transaction\InvMenuTransaction;
use internal\invmenu\transaction\InvMenuTransactionResult;

/**
* Class ExampleMenu
* @package ImperaZim\menus
*/
final class ExampleMenu extends Menu {

  /**
  * Generates and sends the menu to the player.
  */
  public function structure(): IMenu {
    return new InvMenuChest(
      name: $this->getMenuName(),
      contents: $this->getContents(),
      onInteract: fn($transaction) => $this->handleTransaction($transaction),
      onClose: fn($player, $inventory) => $this->handleClose($player, $inventory)
    );
  }

  /**
  * Retrieves the name for the menu.
  * @return string
  */
  private function getMenuName(): string {
    return PluginExample::getSettings('menu_title', 'Example Menu');
  }

  /**
  * Retrieves an array of items for each available class.
  * @return array
  */
  private function getContents(): array {
    $contents = [];
    foreach (PluginExample::getSettings('menu_contents', []) as $button_value => $data) {
      // save as [json_encode(ItemFactory::jsonSerialize($item))]
      $contents[] = ItemFactory::jsonDeserialize(json_decode($itemBlockEncoded));
    }
    $contents[13] = VanillaItems::DIAMOND()->setCustomName('§bCustom Name')->setCount(1);
    return $contents;
  }

  /**
  * Handles the Menu transactions.
  * @param InvMenuTransaction $transaction
  * @return InvMenuTransactionResult|null
  */
  private function handleTransaction(InvMenuTransaction $transaction): ?InvMenuTransactionResult {
    try {
      // Transaction handling logic
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
    return $transaction->continue();
  }

  /**
  * Handles the Menu close.
  * @param Player $player
  * @param Inventory $inventory
  */
  private function handleClose(Player $player, Inventory $inventory): void {
    try {
      // Menu close handling logic
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }
}