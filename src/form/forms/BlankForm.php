<?php

namespace form\forms;

use form\FormInterface;

/**
* Class BlankForm
* A simple implementation of the InstanceForm interface representing a blank form.
* @package form\forms
*/
final class BlankForm implements FormInterface {

  /**
  * BlankForm constructor.
  * @param mixed $a
  * @param mixed $b
  */
  public function __construct(mixed $a, mixed $b) {}

}