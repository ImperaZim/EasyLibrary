<?php

declare(strict_types = 1);

namespace imperazim\components\item\traits;

/**
* Trait ItemRarityTrait
* @package imperazim\components\item\traits
*/
trait ItemRarityTrait {
  
  const COMMON = 'common';
  const UNCOMMON = 'uncommon';
  const RARE = 'rare';
  const EPIC = 'epic';
  const LEGENDARY = 'legendary';

  /** @var string|null */
  protected ?string $rarity = null;

  /** @return string|null */
  public function getRarity(): ?string {
    return $this->rarity;
  }

  /** @param string|null $rarity */
  public function setRarity(?string $rarity): void {
    $this->rarity = $rarity;
  }
  
}