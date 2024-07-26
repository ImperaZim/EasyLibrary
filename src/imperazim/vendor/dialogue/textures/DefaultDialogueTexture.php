<?php

declare(strict_types = 1);

namespace imperazim\vendor\dialogue\textures;

use Generator;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

/**
* Class DefaultDialogueTexture
* @package imperazim\vendor\dialogue\textures
*/
final class DefaultDialogueTexture implements DialogueTexture {

  // Texture constants
  public const TEXTURE__1 = 0;
  public const TEXTURE__2 = 1;
  public const TEXTURE__3 = 2;
  public const TEXTURE__4 = 3;
  public const TEXTURE__5 = 4;
  public const TEXTURE__6 = 5;
  public const TEXTURE__7 = 6;
  public const TEXTURE__8 = 7;
  public const TEXTURE__9 = 8;
  public const TEXTURE__10 = 9;
  public const TEXTURE_SCIENTIST_1 = 10;
  public const TEXTURE_SCIENTIST_2 = 11;
  public const TEXTURE_SCIENTIST_3 = 12;
  public const TEXTURE_SCIENTIST_4 = 13;
  public const TEXTURE_SCIENTIST_5 = 14;
  public const TEXTURE_SCIENTIST_6 = 15;
  public const TEXTURE_SCIENTIST_7 = 16;
  public const TEXTURE_SCIENTIST_8 = 17;
  public const TEXTURE_SCIENTIST_9 = 18;
  public const TEXTURE_SCIENTIST_10 = 19;
  public const TEXTURE_APIARY_1 = 20;
  public const TEXTURE_APIARY_2 = 21;
  public const TEXTURE_APIARY_3 = 22;
  public const TEXTURE_APIARY_4 = 23;
  public const TEXTURE_APIARY_5 = 24;
  public const TEXTURE_TEACHER_1 = 25;
  public const TEXTURE_TEACHER_2 = 26;
  public const TEXTURE_TEACHER_3 = 27;
  public const TEXTURE_TEACHER_4 = 28;
  public const TEXTURE_TEACHER_5 = 29;
  public const TEXTURE_CONSTRUCTION_1 = 30;
  public const TEXTURE_CONSTRUCTION_2 = 31;
  public const TEXTURE_CONSTRUCTION_3 = 32;
  public const TEXTURE_CONSTRUCTION_4 = 33;
  public const TEXTURE_CONSTRUCTION_5 = 34;
  public const TEXTURE_AGRICULTURE_1 = 35;
  public const TEXTURE_AGRICULTURE_2 = 36;
  public const TEXTURE_AGRICULTURE_3 = 37;
  public const TEXTURE_AGRICULTURE_4 = 38;
  public const TEXTURE_AGRICULTURE_5 = 39;
  public const TEXTURE_AGRICULTURE_6 = 40;
  public const TEXTURE_AGRICULTURE_7 = 41;
  public const TEXTURE_AGRICULTURE_8 = 42;
  public const TEXTURE_AGRICULTURE_9 = 43;
  public const TEXTURE_AGRICULTURE_10 = 44;
  public const TEXTURE_BUSINESSMOBS_1 = 45;
  public const TEXTURE_BUSINESSMOBS_2 = 46;
  public const TEXTURE_BUSINESSMOBS_3 = 47;
  public const TEXTURE_BUSINESSMOBS_4 = 48;
  public const TEXTURE_BUSINESSMOBS_5 = 49;
  public const TEXTURE_EVERYDAYBUSINESS_1 = 50;
  public const TEXTURE_EVERYDAYBUSINESS_2 = 51;
  public const TEXTURE_EVERYDAYBUSINESS_3 = 52;
  public const TEXTURE_EVERYDAYBUSINESS_4 = 53;
  public const TEXTURE_EVERYDAYBUSINESS_5 = 54;
  public const TEXTURE_KIOSK_1 = 55;
  public const TEXTURE_KIOSK_2 = 56;
  public const TEXTURE_KIOSK_3 = 57;
  public const TEXTURE_KIOSK_4 = 58;
  public const TEXTURE_KIOSK_5 = 59;

  /** @var EntityDialogueTexture */
  readonly private EntityDialogueTexture $inner;

  /**
  * DefaultDialogueTexture constructor.
  * @param int $texture The texture ID.
  */
  public function __construct(
    readonly private int $texture
  ) {
    $this->inner = new EntityDialogueTexture(EntityIds::NPC);
  }

  /**
  * Applies the texture to the entity.
  * @param int $entity_runtime_id The runtime ID of the entity.
  * @param EntityMetadataCollection $metadata The entity metadata collection.
  * @param Vector3 $pos The position of the entity.
  * @return Generator The generator to apply the texture.
  */
  public function apply(int $entity_runtime_id, EntityMetadataCollection $metadata, Vector3 $pos) : Generator {
    $metadata->setInt(EntityMetadataProperties::VARIANT, $this->texture);
    yield from $this->inner->apply($entity_runtime_id, $metadata, $pos);
  }
}