<?php

declare(strict_types = 1);

namespace imperazim\components\ui\compoments;

use imperazim\vendor\libform\Form as IForm;

/**
* Class Form
* @package imperazim\components\ui\compoments
*/
abstract class Form extends Base {
  
  /**
  * Construct and set up the ui.
  * @return IForm
  */
  protected abstract function structure(): IForm;

  /**
  * Validate the ui type.
  * @param mixed $ui
  * @return bool
  */
  protected function isValid(mixed $ui): bool {
    return $ceof IForm;
  }

}