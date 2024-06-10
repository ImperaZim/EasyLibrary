<?php

declare(strict_types=1);

namespace internal\dialogue\player;

use Logger;
use internal\dialogue\dialogue\Dialogue;
use internal\dialogue\dialogue\DialogueButton;
use internal\dialogue\dialogue\NullDialogue;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\DialoguePacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\types\AbilitiesData;
use pocketmine\network\mcpe\protocol\types\AbilitiesLayer;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\UpdateAbilitiesPacket;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use function array_map;
use function json_encode;
use const JSON_THROW_ON_ERROR;

final class PlayerInstance{

	private bool $modify_abilities = false;
	private ?PlayerDialogueInfo $current_dialogue = null;
	private ?PlayerDialogueInfo $next_dialogue = null;

	public function __construct(
		readonly private PlayerManager $manager,
		readonly private Player $player,
		readonly private Logger $logger
	){}

	public function destroy() : void{
		$dialogue = $this->getCurrentDialogue();
		if($dialogue !== null){
			$dialogue->onPlayerDisconnect($this->player);
			$this->removeCurrentDialogue();
		}
	}

	public function tick() : bool{
		if($this->current_dialogue === null || $this->current_dialogue->status === PlayerDialogueInfo::STATUS_CLOSED || $this->current_dialogue->dialogue !== NullDialogue::instance()){
			return false;
		}
		if(++$this->current_dialogue->tick >= 8){
			$this->onDialogueClose();
		}
		return true;
	}

	public function handleUpdateAbilities(UpdateAbilitiesPacket $packet) : ?UpdateAbilitiesPacket{
		if(!$this->modify_abilities){
			return null;
		}

		$data = $packet->getData();
		$ability_layers = $data->getAbilityLayers();
		foreach($ability_layers as $index => $layer){
			$abilities = $layer->getBoolAbilities();
			if(isset($abilities[AbilitiesLayer::ABILITY_OPERATOR])){
				$abilities[AbilitiesLayer::ABILITY_OPERATOR] = false;
				$ability_layers[$index] = new AbilitiesLayer($layer->getLayerId(), $abilities, $layer->getFlySpeed(), $layer->getWalkSpeed());
			}
		}

		return UpdateAbilitiesPacket::create(new AbilitiesData(
			$data->getCommandPermission(),
			$data->getPlayerPermission(),
			$data->getTargetActorUniqueId(),
			$ability_layers
		));
	}

	public function sendDialogue(Dialogue $dialogue, bool $update = false) : void{
		if($this->current_dialogue !== null && $this->current_dialogue->status !== PlayerDialogueInfo::STATUS_CLOSED){
			if($update){
				$this->current_dialogue->dialogue = $dialogue;
				$this->sendDialogueWindow($this->current_dialogue);
			}else{
				$this->removeCurrentDialogue();
				$this->next_dialogue = new PlayerDialogueInfo($this->current_dialogue->actor_runtime_id, $dialogue, PlayerDialogueInfo::STATUS_SENT, 0);
			}
		}else{
			$this->removeCurrentDialogue();
			$this->current_dialogue = new PlayerDialogueInfo(Entity::nextRuntimeId(), $dialogue, PlayerDialogueInfo::STATUS_SENT, 0);
			$this->sendDialogueInternal($this->current_dialogue);
		}
	}

	private function sendDialogueInternal(PlayerDialogueInfo $info) : void{
		$this->logger->debug("Attempting to send dialogue");
		$session = $this->player->getNetworkSession();
		$metadata = new EntityMetadataCollection();
		$metadata->setGenericFlag(EntityMetadataFlags::IMMOBILE, true);
		$metadata->setByte(EntityMetadataProperties::HAS__COMPONENT, 1);
		foreach($info->dialogue->getTexture()->apply($info->actor_runtime_id, $metadata, new Vector3(0.0, -2.0, 0.0)) as $packet){
			$session->sendDataPacket($packet);
		}
		$this->sendDialogueWindow($info);
	}

	private function sendDialogueWindow(PlayerDialogueInfo $info) : void{
		$session = $this->player->getNetworkSession();
		$is_op = $this->player->hasPermission(DefaultPermissions::ROOT_OPERATOR);
		if($is_op){
			$this->modify_abilities = true;
			$session->syncAbilities($this->player);
		}
		$session->sendDataPacket(DialoguePacket::create(
			$info->actor_runtime_id,
			DialoguePacket::ACTION_OPEN,
			$info->dialogue->getText(),
			(string) $info->actor_runtime_id,
			$info->dialogue->getName(),
			json_encode(array_map(static fn(DialogueButton $button) : array => [
				"button_name" => $button->getName(),
				"text" => $button->getText(),
				"data" => $button->getData(),
				"mode" => $button->getMode(),
				"type" => $button->getType()
			], $info->dialogue->getButtons()), JSON_THROW_ON_ERROR)
		));
		if($is_op){
			$this->modify_abilities = false;
			$session->syncAbilities($this->player);
		}
	}

	public function onDialogueReceive() : void{
		if($this->current_dialogue !== null && $this->current_dialogue->dialogue !== NullDialogue::instance()){
			$this->current_dialogue->status = PlayerDialogueInfo::STATUS_RECEIVED;
		}
	}

	public function onDialogueClose() : void{
		if($this->current_dialogue !== null && $this->current_dialogue->dialogue === NullDialogue::instance()){
			$this->current_dialogue->status = PlayerDialogueInfo::STATUS_CLOSED;
			if($this->next_dialogue !== null){
				$this->current_dialogue = $this->next_dialogue;
				$this->next_dialogue = null;
				$this->sendDialogueInternal($this->current_dialogue);
			}
		}
	}

	public function getCurrentDialogueInfo() : ?PlayerDialogueInfo{
		$dialogue = $this->current_dialogue?->dialogue;
		return $dialogue !== NullDialogue::instance() ? $this->current_dialogue : null;
	}

	public function getCurrentDialogue() : ?Dialogue{
		return $this->getCurrentDialogueInfo()?->dialogue;
	}

	public function onDialogueRespond(string $scene_name, int $index) : void{
		$info = $this->getCurrentDialogueInfo();
		if($info !== null && (int) $scene_name === $info->actor_runtime_id){
			if(isset($info->dialogue->getButtons()[$index])){
				$info->dialogue->onPlayerRespond($this->player, $index);
			}else{
				$info->dialogue->onPlayerRespondInvalid($this->player, $index);
			}
		}
	}

	public function removeCurrentDialogue() : ?PlayerDialogueInfo{
		if($this->current_dialogue === null || $this->current_dialogue->dialogue === NullDialogue::instance()){
			return null;
		}

		$this->logger->debug("Closed dialogue");
		$current_dialogue = $this->current_dialogue;
		$current_dialogue->dialogue->onPlayerClose($this->player);
		$current_dialogue->dialogue = NullDialogue::instance();

		$session = $this->player->getNetworkSession();
		$session->sendDataPacket(DialoguePacket::create($current_dialogue->actor_runtime_id, DialoguePacket::ACTION_CLOSE, "", "", "", ""));
		$session->sendDataPacket(RemoveActorPacket::create($current_dialogue->actor_runtime_id));
		$this->manager->tick($this->player);
		return $current_dialogue;
	}
}