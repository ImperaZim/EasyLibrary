<?php

declare(strict_types = 1);

namespace imperazim\components\item\traits;

/**
* Trait EvolutionaryItemTrait
* @package imperazim\components\item\traits
*/
trait EvolutionaryItemTrait {

  /** @var int|null */
  protected ?int $level = null;

  /** @var int|null */
  protected ?int $minLevel = null;

  /** @var int|null */
  protected ?int $maxLevel = null;

  /** @return int|null */
  public function getLevel(): ?int {
    return $this->level;
  }

  /** @param int|null $level */
  public function setLevel(?int $level): void {
    $this->level = $level;
  }

  /** @return int|null */
  public function getMinLevel(): ?int {
    return $this->minLevel;
  }

  /** @param int|null $minLevel */
  public function setMinLevel(?int $minLevel): void {
    $this->minLevel = $minLevel;
  }

  /** @return int|null */
  public function getMaxLevel(): ?int {
    return $this->maxLevel;
  }

  /** @param int|null $maxLevel */
  public function setMaxLevel(?int $maxLevel): void {
    $this->maxLevel = $maxLevel;
  }

  /**
  * Check if the current level is within the defined min and max levels.
  * @return bool
  */
  public function isLevelWithinBounds(): bool {
    if ($this->level === null) {
      return false;
    }
    return ($this->minLevel === null || $this->level >= $this->minLevel) &&
    ($this->maxLevel === null || $this->level <= $this->maxLevel);
  }

  /**
  * Increment the level by a specified amount.
  * @param int $increment
  * @return void
  */
  public function incrementLevel(int $increment = 1): void {
    if ($this->level === null) {
      $this->level = 0;
    }
    $this->level += $increment;
    if ($this->maxLevel !== null && $this->level > $this->maxLevel) {
      $this->level = $this->maxLevel;
    }
  }

  /**
  * Decrement the level by a specified amount.
  * @param int $decrement
  * @return void
  */
  public function decrementLevel(int $decrement = 1): void {
    if ($this->level === null) {
      $this->level = 0;
    }
    $this->level -= $decrement;
    if ($this->minLevel !== null && $this->level < $this->minLevel) {
      $this->level = $this->minLevel;
    }
  }
  
}