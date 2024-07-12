<?php

declare(strict_types=1);

namespace internal\customitem\item;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\item\Durable;

class CustomDurableItem extends Durable{
	use CustomItemTrait;

	public function getMaxDurability() : int{
		return $this->getProperties()->getMaxDurability();
	}

	public function onDestroyBlock(Block $block, array &$returnedItems) : bool{
		return $this->applyDamage(1);
	}

	public function onAttackEntity(Entity $victim, array &$returnedItems) : bool{
		return $this->applyDamage(1);
	}
}