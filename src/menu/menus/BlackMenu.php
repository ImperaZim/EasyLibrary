<?php

namespace menu\menus;

use menu\InvMenuInterface;

/**
* Class BlankMenu
* A simple implementation of the InstanceInvMenu interface representing a blank menu.
* @package menu\menus
*/
final class BlankMenu implements InvMenuInterface {

  /**
  * BlankMenu constructor.
  * @param mixed $a
  * @param mixed $b
  */
  public function __construct(mixed $a, mixed $b) {}

}