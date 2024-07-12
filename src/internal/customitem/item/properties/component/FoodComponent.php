<?php

declare(strict_types=1);

namespace internal\customitem\item\properties\component;

use internal\customitem\util\InvalidNBTStateException;
use pocketmine\nbt\tag\CompoundTag;

/**
 * This component makes the item edible.
 */
final class FoodComponent extends Component{

	public const TAG_FOOD = "minecraft:food";

	public function __construct(
		private readonly bool $canAlwaysEat,
		private readonly int $nutrition,
		private readonly float $saturationModifier = 0.6
	){ }

	public function getName() : string{
		return "food";
	}

	public function buildComponent(CompoundTag $rootNBT) : void{
		$componentNBT = $rootNBT->getCompoundTag(Component::TAG_COMPONENTS);
		if($componentNBT === null){
			throw new InvalidNBTStateException("Component tree is not built");
		}
		$componentNBT->setTag(self::TAG_FOOD, CompoundTag::create());
	}

	public function processComponent(CompoundTag $rootNBT) : void{
		$foodTag = $rootNBT->getCompoundTag(Component::TAG_COMPONENTS)?->getCompoundTag(self::TAG_FOOD);
		if($foodTag === null){
			throw new InvalidNBTStateException("Component tree is not built");
		}
		$foodTag->setByte("can_always_eat", $this->canAlwaysEat ? 1 : 0);
		$foodTag->setInt("nutrition", $this->nutrition);
		$foodTag->setFloat("saturation_modifier", $this->saturationModifier);
	}
}