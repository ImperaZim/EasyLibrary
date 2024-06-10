<?php

declare(strict_types=1);

namespace internal\dialogue\dialogue\texture;

use Generator;
use pocketmine\entity\Skin;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\PlayerSkinPacket;
use pocketmine\network\mcpe\protocol\types\AbilitiesData;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\network\mcpe\protocol\types\GameMode;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\network\mcpe\protocol\types\skin\SkinData;
use pocketmine\network\mcpe\protocol\UpdateAbilitiesPacket;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function json_encode;
use const JSON_THROW_ON_ERROR;

final class PlayerDialogueTexture implements DialogueTexture{

	readonly public SkinData $skin_data;
	readonly public string $skin_index;
	readonly public UuidInterface $uuid;

	public function __construct(Skin $skin, ?DialogueTextureOffset $picker_offset = null, ?DialogueTextureOffset $portrait_offset = null){
		$this->uuid = Uuid::uuid4();
		$this->skin_data = TypeConverter::getInstance()->getSkinAdapter()->toSkinData($skin);
		$this->skin_index = json_encode([
			"picker_offsets" => $picker_offset ?? DialogueTextureOffset::defaultPicker(),
			"portrait_offsets" => $portrait_offset ?? DialogueTextureOffset::defaultPlayerPortrait()
		], JSON_THROW_ON_ERROR);
	}

	public function apply(int $entity_runtime_id, EntityMetadataCollection $metadata, Vector3 $pos) : Generator{
		$metadata->setString(EntityMetadataProperties::_SKIN_INDEX, $this->skin_index);
		yield AddPlayerPacket::create(
			$this->uuid,
			"",
			$entity_runtime_id,
			"",
			$pos,
			null,
			0.0,
			0.0,
			0.0,
			ItemStackWrapper::legacy(ItemStack::null()),
			GameMode::SURVIVAL,
			$metadata->getAll(),
			new PropertySyncData([], []),
			UpdateAbilitiesPacket::create(new AbilitiesData(0, 0, $entity_runtime_id, [])),
			[],
			"",
			DeviceOS::UNKNOWN
		);
		yield PlayerSkinPacket::create($this->uuid, "a", "b", $this->skin_data);
	}
}