<?php

declare(strict_types = 1);

namespace imperazim\vendor\libform\traits;

/**
* Trait IdentifiableElement
* @package imperazim\vendor\libform\traits
*/
trait IdentifiableElement {

  /** @var mixed */
  private mixed $identifier = null;

  /**
  * Gets the identifier of the element.
  * @return mixed The identifier of the element.
  */
  public function getIdentifier(): mixed {
    return $this->identifier;
  }

  /**
  * Sets the identifier of the element.
  * @param mixed $identifier The new identifier of the element.
  */
  public function setIdentifier(mixed $identifier): void {
    $this->identifier = $identifier;
  }

}