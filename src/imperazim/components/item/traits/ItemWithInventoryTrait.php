<?php

declare(strict_types = 1);

namespace imperazim\components\item\traits; 

use imperazim\components\item\ItemFactory;

/**
* Trait ItemWithInventoryTrait
* @package imperazim\components\item\traits
*/
trait ItemWithInventoryTrait {

  /** @var string|null */
  protected ?string $contents = null;

  /** @return array */
  public function getContents(): array {
    $items = [];
    foreach (json_decode($this->contents) as $slot => $item) {
      $items[$slot] = ItemFactory::jsonDeserialize($item);
    }
    return $items;
  }

  /** @param array $contents */
  public function setContents(array $contents): void {
    $items = [];
    foreach ($contents as $slot => $item) {
      $items[$slot] = ItemFactory::jsonSerialize($item);
    }
    $this->contents = json_encode($items);
  }

}