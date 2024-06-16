<?php

declare(strict_types = 1);

namespace library\interface;

use internal\dialogue\Dialogue as IDialogue;

/**
* Class Dialogue
* @package library\interface
*/
abstract class Dialogue extends BaseInterface {
  
  /**
  * Construct and set up the interface.
  * @return IDialogue
  */
  protected abstract function structure(): IDialogue;

  /**
  * Validate the interface type.
  * @param mixed $interface
  * @return bool
  */
  protected function isValidInterface(mixed $interface): bool {
    return $interface instanceof IDialogue;
  }

}