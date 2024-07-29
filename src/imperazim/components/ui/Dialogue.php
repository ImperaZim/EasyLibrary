<?php

declare(strict_types = 1);

namespace imperazim\components\ui;

use imperazim\vendor\dialogue\Dialogue as IDialogue;

/**
* Class Dialogue
* @package imperazim\components\ui
*/
abstract class Dialogue extends Base {
  
  /**
  * Construct and set up the ui.
  * @return IDialogue
  */
  protected abstract function structure(): IDialogue;

  /**
  * Validate the ui type.
  * @param mixed $ui
  * @return bool
  */
  protected function isValid(mixed $ui): bool {
    return $ui instanceof IDialogue;
  }

}