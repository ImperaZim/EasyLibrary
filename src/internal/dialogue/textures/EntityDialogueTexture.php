<?php

declare(strict_types = 1);

namespace internal\dialogue\textures;

use Generator;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;

/**
* Class EntityDialogueTexture
* Implements the DialogueTexture interface to apply a texture to an entity.
*
* @package internal\dialogue\textures
*/
final class EntityDialogueTexture implements DialogueTexture {

  /**
  * @param EntityIds::*|string $entity_network_id The network ID of the entity.
  */
  public function __construct(
    readonly private string $entity_network_id
  ) {}

  /**
  * Applies the dialogue texture to the entity.
  * @param int $entity_runtime_id The runtime ID of the entity.
  * @param EntityMetadataCollection $metadata The metadata collection for the entity.
  * @param Vector3 $pos The position of the entity.
  * @return Generator Yields an AddActorPacket with the entity's properties.
  */
  public function apply(int $entity_runtime_id, EntityMetadataCollection $metadata, Vector3 $pos) : Generator {
    $metadata->setGenericFlag(EntityMetadataFlags::BABY, true);
    yield AddActorPacket::create(
      $entity_runtime_id,
      $entity_runtime_id,
      $this->entity_network_id,
      $pos,
      null,
      0.0,
      0.0,
      0.0,
      0.0,
      [],
      $metadata->getAll(),
      new PropertySyncData([], []),
      []
    );
  }
}