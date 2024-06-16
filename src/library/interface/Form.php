<?php

declare(strict_types = 1);

namespace library\interface;

use internal\libform\Form as IForm;

/**
* Class Dialogue
* @package library\interface
*/
abstract class Dialogue extends BaseInterface {
  
  /**
  * Construct and set up the interface.
  * @return IForm
  */
  protected abstract function structure(): IForm;

  /**
  * Validate the interface type.
  * @param mixed $interface
  * @return bool
  */
  protected function isValidInterface(mixed $interface): bool {
    return $interface instanceof IForm;
  }

}