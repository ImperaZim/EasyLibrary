<?php

declare(strict_types = 1);

namespace imperazim\components\item\traits;

/**
* Trait UsableItemTrait
* @package imperazim\components\item\traits
*/
trait UsableItemTrait {

  /** @var float|null */
  protected ?float $lastUsedTime = null;

  /** @var int */
  protected int $cooldownPeriod = 60;

  /**
  * Sets the time when the item was last used to the current microtime.
  */
  public function setLastUsedTime(): void {
    $this->lastUsedTime = microtime(true);
  }

  /**
  * Gets the time when the item was last used.
  * @return float|null
  */
  public function getLastUsedTime(): ?float {
    return $this->lastUsedTime;
  }

  /**
  * Sets the cooldown period.
  * @param int $seconds
  */
  public function setCooldownPeriod(int $seconds): void {
    $this->cooldownPeriod = $seconds;
  }

  /**
  * Gets the cooldown period.
  * @return int
  */
  public function getCooldownPeriod(): int {
    return $this->cooldownPeriod;
  }

  /**
  * Checks if the cooldown period has passed since the item was last used.
  * @return bool
  */
  public function isCooldownComplete(): bool {
    if ($this->lastUsedTime === null) {
      return true;
    }

    $currentTime = microtime(true);
    return ($currentTime - $this->lastUsedTime) >= $this->cooldownPeriod;
  }

  /**
  * Gets the remaining cooldown time.
  * @return float
  */
  public function getRemainingCooldownTime(): float {
    if ($this->lastUsedTime === null) {
      return 0.0;
    }

    $currentTime = microtime(true);
    $remainingTime = $this->cooldownPeriod - ($currentTime - $this->lastUsedTime);
    return $remainingTime > 0 ? $remainingTime : 0.0;
  }
  
  public function renderProgressBar(?string $zero = '§7|', ?string $one = '§e|', ?int $length = 20): string {
    $cooldownPeriod = $this->getCooldownPeriod();
    $progress = ($cooldownPeriod - $this->getRemainingCooldownTime()) / $cooldownPeriod;
    $completeLength = (int) round($progress * $length);
    $progressBar = str_repeat($one, $completeLength) . str_repeat($zero, $length - $completeLength);
    return $progressBar;
}
}