<?php

declare(strict_types=1);

namespace internal\customitem\item;

use internal\customitem\item\properties\CustomItemProperties;
use pocketmine\item\ItemIdentifier;

trait CustomItemTrait{
	/** @var CustomItemProperties */
	protected CustomItemProperties $properties;

	public function __construct(string $name, CustomItemProperties $properties){
		$this->properties = $properties;
		parent::__construct(new ItemIdentifier($this->properties->getId()), $this->properties->getName());
	}

	public function getProperties() : CustomItemProperties{
		return $this->properties;
	}

	public function getAttackPoints() : int{
		return $this->properties->getAttackPoints();
	}

	public function getCooldownTicks() : int{
		return $this->properties->getCooldown();
	}

	public function getBlockToolType() : int{
		return $this->properties->getBlockToolType();
	}

	public function getBlockToolHarvestLevel() : int{
		return $this->properties->getBlockToolHarvestLevel();
	}

	// TODO: This needs to be fixed in order to display break progress correctly.
	public function getMiningEfficiency(bool $isCorrectTool) : float{
		return $this->properties->getMiningSpeed();
	}

	public function getMaxStackSize() : int{
		return $this->getProperties()->getMaxStackSize();
	}
}