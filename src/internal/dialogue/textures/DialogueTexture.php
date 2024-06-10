<?php

declare(strict_types=1);

namespace internal\dialogue\textures;

use Generator;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;

interface DialogueTexture{

	/**
  * Applies the texture to the entity.
  *
  * @param int $entity_runtime_id The runtime ID of the entity.
  * @param EntityMetadataCollection $metadata The entity metadata collection.
  * @param Vector3 $pos The position of the entity.
  * @return Generator The generator to apply the texture.
  */
	public function apply(int $entity_runtime_id, EntityMetadataCollection $metadata, Vector3 $pos) : Generator;
}