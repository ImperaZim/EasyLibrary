<?php

declare(strict_types = 1);

namespace library\interface;

use internal\invmenu\InvMenu as IMenu;

/**
* Class Dialogue
* @package library\interface
*/
abstract class Dialogue extends BaseInterface {
  
  /**
  * Construct and set up the interface.
  * @return IMenu
  */
  protected abstract function structure(): IMenu;

  /**
  * Validate the interface type.
  * @param mixed $interface
  * @return bool
  */
  protected function isValidInterface(mixed $interface): bool {
    return $interface instanceof IMenu;
  }

}