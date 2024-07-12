<?php

declare(strict_types=1);

namespace internal\customitem\item;

use pocketmine\item\Tool;

final class CustomToolItem extends Tool{
	use CustomItemTrait {
		getMiningEfficiency as customGetMiningEfficiency;
	}

	public function getMaxDurability() : int{
		return $this->properties->getMaxDurability();
	}

	protected function getBaseMiningEfficiency() : float{
		return $this->customGetMiningEfficiency(true);
	}
}