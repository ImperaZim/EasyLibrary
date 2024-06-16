<?php

declare(strict_types = 1);

namespace internal\invmenu\types;

use Closure;
use internal\invmenu\InvMenu;
use internal\invmenu\InvMenuHooker;
use internal\invmenu\type\InvMenuTypeIds;

/** 
* Class InvMenuDoubleChest
* @package internal\invmenu\types
*/
class InvMenuDoubleChest extends InvMenu {

  /**
  * InvMenuDoubleChest constructor.
  * @param string $name
  * @param array $contents
  * @param (Closure(InvMenuTransaction) : InvMenuTransactionResult)|null
  * @param (Closure(Player, Inventory) : void)|null
  */
  public function __construct(
    ?string $name = '',
    ?array $contents = [],
    private ?Closure $onInteract = null,
    private ?Closure $onClose = null
  ) {
    parent::__construct(InvMenuHooker::getTypeRegistry()->get(InvMenuTypeIds::TYPE_DOUBLE_CHEST));
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