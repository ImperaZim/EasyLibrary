<?php

declare(strict_types = 1);

namespace imperazim\vendor\commando;

use imperazim\vendor\commando\constraint\BaseConstraint;

/**
* Interface IRunnable
*
* An interface which is declares the minimum required information
* to get background information for a command and/or a sub-command
*
* @package imperazim\vendor\commando
*/
interface IRunnable {
  public function getName(): string;

  /**
  * @return string[]
  */
  public function getAliases(): array;

  public function getUsageMessage():string;

  /**
  * @return string[]
  */
  public function getPermissions(): array;

  /**
  * @return BaseConstraint[]
  */
  public function getConstraints():array;
}