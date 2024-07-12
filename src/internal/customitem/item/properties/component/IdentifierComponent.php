<?php

declare(strict_types=1);

namespace internal\customitem\item\properties\component;

use pocketmine\nbt\tag\CompoundTag;

/**
 * This component tells the client which item it is.
 * It is essential for the client to render the item correctly.
 */
final class IdentifierComponent extends Component{

	public const TAG_IDENTIFIER = "minecraft:identifier";

	public function __construct(private readonly int $runtimeId){}

	public function getName() : string{
		return "identifier";
	}

	public function processComponent(CompoundTag $rootNBT) : void{
		$rootNBT->setShort(self::TAG_IDENTIFIER, $this->runtimeId);
	}
}