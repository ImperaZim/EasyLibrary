<?php

declare(strict_types=1);

namespace internal\customitem\item\properties\component;

use pocketmine\nbt\tag\CompoundTag;

/**
 * Base class for components.
 */
abstract class Component{

	public const TAG_COMPONENTS = "components";

	abstract public function getName() : string;

	/**
	 * Builds the basic component tree which will be used to process the component.
	 */
	public function buildComponent(CompoundTag $rootNBT) : void{
	}

	/**
	 * Processes the component.
	 * This method assumes the component tree is already built.
	 */
	abstract public function processComponent(CompoundTag $rootNBT) : void;
}