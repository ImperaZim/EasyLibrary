<?php

declare(strict_types=1);

namespace internal\customitem\item;

use pocketmine\item\Food;
use pocketmine\item\Item;

class CustomFoodItem extends Food{
	use CustomItemTrait;

	public function getMaxStackSize() : int{
		return $this->getProperties()->getMaxStackSize();
	}

	public function getFoodRestore() : int{
		return $this->getProperties()->getNutrition();
	}

	public function requiresHunger() : bool{
		return !$this->getProperties()->getCanAlwaysEat();
	}

	public function getSaturationRestore() : float{
		return $this->getProperties()->getSaturation();
	}

	public function getResidue() : Item{
		return $this->getProperties()->getResidue();
	}
}
