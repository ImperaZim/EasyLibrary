<?php

declare(strict_types=1);

namespace internal\customitem\item\properties\component;

use internal\customitem\util\InvalidNBTStateException;
use pocketmine\nbt\tag\CompoundTag;

/**
 * This component tells the client to set the name of item.
 * This is essential component for all custom items.
 */
final class DisplayNameComponent extends Component{

	public const TAG_DISPLAY_NAME = "minecraft:display_name";

	public function __construct(private readonly string $displayName){ }

	public function getName() : string{
		return "texture";
	}

	public function buildComponent(CompoundTag $rootNBT) : void{
		$componentTag = $rootNBT->getCompoundTag(Component::TAG_COMPONENTS);
		if($componentTag === null){
			throw new InvalidNBTStateException("Component tree is not built");
		}
		$componentTag->setTag(self::TAG_DISPLAY_NAME, CompoundTag::create());
	}

	public function processComponent(CompoundTag $rootNBT) : void{
		$displayNameTag = $rootNBT->getCompoundTag(Component::TAG_COMPONENTS)?->getCompoundTag(self::TAG_DISPLAY_NAME);
		if($displayNameTag === null){
			throw new InvalidNBTStateException("Component tree is not built");
		}
		$displayNameTag->setString("value", $this->displayName);
	}
}