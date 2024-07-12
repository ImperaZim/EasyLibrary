<?php

declare(strict_types=1);

namespace internal\customitem\item\properties\component;

use internal\customitem\util\InvalidNBTStateException;
use pocketmine\nbt\tag\CompoundTag;

/**
 * This component makes item have cooldown.
 */
final class CooldownComponent extends Component{

	public const TAG_COOLDOWN = "minecraft:cooldown";

	public function __construct(private readonly int $cooldown){ }

	public function getName() : string{
		return "cooldown";
	}

	public function buildComponent(CompoundTag $rootNBT) : void{
		$componentNBT = $rootNBT->getCompoundTag(Component::TAG_COMPONENTS);
		if($componentNBT === null){
			throw new InvalidNBTStateException("Component tree is not built");
		}
		$componentNBT->setTag(self::TAG_COOLDOWN, CompoundTag::create());
	}

	public function processComponent(CompoundTag $rootNBT) : void{
		$cooldownTag = $rootNBT->getCompoundTag(Component::TAG_COMPONENTS)?->getCompoundTag(self::TAG_COOLDOWN);
		if($cooldownTag === null){
			throw new InvalidNBTStateException("Component tree is not built");
		}
		$cooldownTag->setString("category", "attack"); // TODO: Find out this
		$cooldownTag->setFloat("value", $this->cooldown / 20); // maybe in tick
	}
}