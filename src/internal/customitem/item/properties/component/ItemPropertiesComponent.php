<?php

declare(strict_types=1);

namespace internal\customitem\item\properties\component;

use internal\customitem\util\InvalidNBTStateException;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\Tag;

/**
 * This component contains all the properties of the item.
 * You can use {@link ItemPropertiesComponent::addComponent() addComponent()} method to add components to the item properties component.
 */
final class ItemPropertiesComponent extends Component{

	public const TAG_ITEM_PROPERTIES = "item_properties";
	public const TAG_ICON = "minecraft:icon";
	public const TAG_USE_DURATION = "use_duration";

	/** @var array<string, Tag> tagName => NbtTag */
	private array $properties = [];

	public function getName() : string{
		return "item_properties";
	}

	public function buildComponent(CompoundTag $rootNBT) : void{
		$componentNBT = $rootNBT->getCompoundTag(Component::TAG_COMPONENTS);
		if($componentNBT === null){
			throw new InvalidNBTStateException("Component tree is not built");
		}
		$componentNBT->setTag(self::TAG_ITEM_PROPERTIES, CompoundTag::create());
	}

	public function processComponent(CompoundTag $rootNBT) : void{
		$componentNBT = $rootNBT->getCompoundTag(Component::TAG_COMPONENTS)?->getCompoundTag(self::TAG_ITEM_PROPERTIES);
		if($componentNBT === null){
			throw new InvalidNBTStateException("Component tree is not built");
		}
		$requiredPropertiesFound = [
			self::TAG_ICON => false,
			self::TAG_USE_DURATION => false
		];
		foreach($this->properties as $tagName => $nbt){
			if(isset($requiredPropertiesFound[$tagName])){
				$requiredPropertiesFound[$tagName] = true;
			}
			$componentNBT->setTag($tagName, $nbt);
		}
		foreach($requiredPropertiesFound as $tagName => $found){
			if(!$found){
				throw new InvalidNBTStateException("Required property $tagName is not found");
			}
		}
	}

	/**
	 * Adds component to the item properties component.
	 */
	public function addComponent(string $tagName, Tag $nbt) : void{
		$this->properties[$tagName] = $nbt;
	}

	/**
	 * @param string $iconPath The name of texture without its extension
	 * @param string $legacyId The namespace id of the item
	 */
	public function setIcon(string $iconPath, string $legacyId) : void{
		$this->addComponent(self::TAG_ICON, CompoundTag::create()
			->setTag("textures", CompoundTag::create()->setString("default", $iconPath))
			->setString("legacy_id", $legacyId)
		);
	}
}
