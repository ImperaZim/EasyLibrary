<?php

declare(strict_types = 1);

namespace imperazim\harvest\custom;

use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\block\Crops;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\item\ItemUseResult;
use pocketmine\item\ItemIdentifier;
use pocketmine\world\particle\WaterDripParticle;

use internal\customies\item\ItemComponents;
use internal\customies\item\ItemComponentsTrait;
use internal\customies\item\CustomiesItemFactory;
use internal\customies\item\CreativeInventoryInfo;

/**
* Class Ruby
* @package com\imperazim\harvest\custom
*/
class Ruby extends Item implements ItemComponents {
  use ItemComponentsTrait;

  public const NAME = 'Ruby';
  public const IDENTIFIER = 'ruby';

  /**
  * Ruby constructor.
  * @param ItemIdentifier $identifier The identifier.
  * @param string $name The name.
  */
  public function __construct(
    private ItemIdentifier $identifier,
    protected string $name = self::NAME
  ) {
    parent::__construct($identifier, $name);
    $this->initComponent(
      self::IDENTIFIER,
      new CreativeInventoryInfo(
        CreativeInventoryInfo::CATEGORY_ITEMS
      )
    );
    $this->setupRenderOffsets(24, 24, false);
  }

  /**
  * Get max stack from this item.
  * @return int
  */
  public function getMaxStackSize(): int {
    return 64;
  }

}