<?php

declare(strict_types = 1);

namespace menu\menus;

use menu\InvMenuBase;
use pocketmine\player\Player;
use libraries\invmenu\InvMenu;
use pocketmine\item\VanillaItems;
use libraries\invmenu\InvMenuChest;
use pocketmine\inventory\Inventory;
use libraries\invmenu\transaction\InvMenuTransaction;
use libraries\invmenu\transaction\InvMenuTransactionResult;

/**
* Class ExampleMenu
* @package menu\menus
*/
final class ExampleMenu extends InvMenuBase {

  /**
  * Generates and sends the menu to the player.
  */
  public function makeMenu(): void {
    try {
      /**
      * TYPES: InvMenuChest, InvMenuDoubleChest, InvMenuHopper
      * I only left one example because the structure is the same for all types.
      */
      $menu = new InvMenuChest(
        name: $this->getMenuName(),
        contents: $this->getContents(),
        onInteract: fn($transaction) => $this->handleTransaction($transaction),
        onClose: fn($player, $inventory) => $this->handleClose($player, $inventory)
      );
      $this->setMenuBase($menu)->send();
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Retrieves the name for the menu.
  * @return string
  */
  private function getMenuName(): string {
    return 'Example Chest Menu';
  }

  /**
  * Retrieves an array of items for each available class.
  * @return array[slot => item]
  */
  private function getContents(): array {
    $items = [];
    $items[0] = VanillaItems::APPLE();
    $items[2] = VanillaItems::DIAMOND_SWORD()->setCustomName('§r§' . $this->getPlayer()->getName() . '\'s Sword');
    return $items;
  }

  /**
  * Handles the InvMenu transactions.
  * @param InvMenuTransaction $transaction
  * @return InvMenuTransactionResult|null
  */
  private function handleTransaction(InvMenuTransaction $transaction): ?InvMenuTransactionResult {
    try {
      $player = $transaction->getPlayer();
      $item = $transaction->getItemClicked();

      $player->removeCurrentWindow();
      $player->sendMessage($item->getName() . ' clicked');
      return $transaction->discard();
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
    $this->getPlayer()->removeCurrentWindow();
    return null;
  }

  /**
  * Handles the InvMenu close.
  * @param Player $player
  * @param Inventory $inventory
  */
  private function handleClose(Player $player, Inventory $inventory): void {
    try {
      $player->sendMessage('Menu closed!');
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }
}