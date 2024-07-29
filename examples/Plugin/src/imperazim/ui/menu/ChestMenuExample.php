<?php

declare(strict_types = 1);

namespace imperazim\ui\menu;

use pocketmine\player\Player;
use pocketmine\inventory\Inventory;
use pocketmine\item\StringToItemParser;
use pocketmine\item\LegacyStringToItemParser;

use imperazim\ui\UiManager;
use imperazim\components\ui\Menu;
use imperazim\vendor\invmenu\InvMenu as IMenu;
use imperazim\vendor\invmenu\types\InvMenuChest;
use imperazim\vendor\invmenu\transaction\InvMenuTransaction;
use imperazim\vendor\invmenu\transaction\InvMenuTransactionResult;

/**
* Class ChestMenuExample
* @package imperazim\ui\menu
*/
final class ChestMenuExample extends Menu {

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
    $file = UiManager::getFile('forms');
    return $file->get('chest_menu.title', '');
  }

  /**
  * Retrieves an array of items for each available class.
  * @return array
  */
  private function getContents(): array {
    $contents = [];
    $file = UiManager::getFile('forms');
    $contentList = $file->get('chest_menu.contents', []);

    foreach ($contentList as $slot => $itemName) {
      try {
        $contents[] = StringToItemParser::getInstance()->parse($itemName) ?? LegacyStringToItemParser::getInstance()->parse($itemName);
      }catch(\Exception $e) {
        new crashdump($e);
      }
    }

    return $contents;
  }

  /**
  * Handles the Menu transactions.
  * @param InvMenuTransaction $transaction
  * @return InvMenuTransactionResult|null
  */
  private function handleTransaction(InvMenuTransaction $transaction): ?InvMenuTransactionResult {
    return $transaction->continue();
  }

  /**
  * Handles the Menu close.
  * @param Player $player
  * @param Inventory $inventory
  */
  private function handleClose(Player $player, Inventory $inventory): void {
    $player->sendMessage('Chest Menu closed!');
  }
}