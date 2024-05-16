<?php

declare(strict_types = 1);

namespace libraries\invmenu;

use Closure;
use pocketmine\player\Player;
use libraries\invmenu\type\InvMenuTypeIds;

/**
* Class InvMenuHopper
* @package libraries\invmenu
*/
class InvMenuHopper extends InvMenu {

  /**
  * InvMenuHopper constructor.
  * @param string $name
  * @param array $contents
  * @param (Closure(InvMenuTransaction) : InvMenuTransactionResult)|null
  * @param (Closure(Player, Inventory) : void)|null
  */
  public function __construct(
    string $name,
    array $contents = [],
    private ?Closure $onInteract = null,
    private ?Closure $onClose = null
  ) {
    parent::__construct(InvMenuHandler::getTypeRegistry()->get(InvMenuTypeIds::TYPE_HOPPER));
    $this->setName($name);
    $this->setListener($onInteract);
    $this->setInventoryCloseListener($onClose);
    if (!empty($contents)) {
      foreach ($contents as $index => $item) {
        $this->getInventory()->setItem($index, $item);
      }
    }
  }

}