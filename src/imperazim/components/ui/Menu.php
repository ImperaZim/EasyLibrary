<?php

declare(strict_types = 1);

namespace imperazim\components\ui;

use imperazim\vendor\invmenu\InvMenu as IMenu;

/**
* Class Menu
* @package imperazim\components\ui
*/
abstract class Menu extends Base {
  
  /**
  * Construct and set up the ui.
  * @return IMenu
  */
  protected abstract function structure(): IMenu;

  /**
  * Validate the ui type.
  * @param mixed $ui
  * @return bool
  */
  protected function isValid(mixed $ui): bool {
    return $ui instanceof IMenu;
  }

}