<?php

declare(strict_types=1);

namespace internal\dialogue\player;

use Logger;
use pocketmine\math\Vector3;
use pocketmine\entity\Entity;
use pocketmine\player\Player;
use internal\dialogue\Dialogue;
use internal\dialogue\types\NullDialogue;
use pocketmine\permission\DefaultPermissions;
use internal\dialogue\elements\DialogueButton;
use pocketmine\network\mcpe\protocol\NpcDialoguePacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\types\AbilitiesData;
use pocketmine\network\mcpe\protocol\types\AbilitiesLayer;
use pocketmine\network\mcpe\protocol\UpdateAbilitiesPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

use function array_map;
use function json_encode;
use const JSON_THROW_ON_ERROR;

/**
 * Class PlayerInstance
 * @package internal\dialogue\player
 */
final class PlayerInstance{

	private bool $modify_abilities = false;
	private ?PlayerDialogueInfo $current_dialogue = null;
	private ?PlayerDialogueInfo $next_dialogue = null;

	/**
	 * PlayerInstance constructor.
	 * @param PlayerManager $manager The manager for player dialogues.
	 * @param Player $player The player instance.
	 * @param Logger $logger The logger instance.
	 */
	public function __construct(
		readonly private PlayerManager $manager,
		readonly private Player $player,
		readonly private Logger $logger
	){}

	/**
	 * Destroys the current dialogue and handles player disconnect.
	 */
	public function destroy() : void{
		$dialogue = $this->getCurrentDialogue();
		if($dialogue !== null){
			$dialogue->onPlayerDisconnect($this->player);
			$this->removeCurrentDialogue();
		}
	}

	/**
	 * Handles dialogue ticking.
	 * @return bool True if a dialogue is active and not closed, false otherwise.
	 */
	public function tick() : bool{
		if($this->current_dialogue === null || $this->current_dialogue->status === PlayerDialogueInfo::STATUS_CLOSED || $this->current_dialogue->dialogue !== NullDialogue::instance()){
			return false;
		}
		if(++$this->current_dialogue->tick >= 8){
			$this->onDialogueClose();
		}
		return true;
	}

	/**
	 * Handles the update of player abilities.
	 * @param UpdateAbilitiesPacket $packet The update abilities packet.
	 * @return UpdateAbilitiesPacket|null The modified abilities packet or null if not modified.
	 */
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

	/**
	 * Sends a dialogue to the player.
	 * @param Dialogue $dialogue The dialogue instance.
	 * @param bool $update Whether to update the current dialogue.
	 */
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

	/**
	 * Sends the dialogue internally.
	 * @param PlayerDialogueInfo $info The player dialogue info.
	 */
	private function sendDialogueInternal(PlayerDialogueInfo $info) : void{
		$this->logger->debug("Attempting to send dialogue");
		$session = $this->player->getNetworkSession();
		$metadata = new EntityMetadataCollection();
		$metadata->setGenericFlag(EntityMetadataFlags::IMMOBILE, true);
		$metadata->setByte(EntityMetadataProperties::HAS_NPC_COMPONENT, 1);
		foreach($info->dialogue->getTexture()->apply($info->actor_runtime_id, $metadata, new Vector3(0.0, -2.0, 0.0)) as $packet){
			$session->sendDataPacket($packet);
		}
		$this->sendDialogueWindow($info);
	}

	/**
	 * Sends the dialogue window.
	 * @param PlayerDialogueInfo $info The player dialogue info.
	 */
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

	/**
	 * Handles the dialogue reception.
	 */
	public function onDialogueReceive() : void{
		if($this->current_dialogue !== null && $this->current_dialogue->dialogue !== NullDialogue::instance()){
			$this->current_dialogue->status = PlayerDialogueInfo::STATUS_RECEIVED;
		}
	}

	/**
	 * Handles the dialogue closure.
	 */
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

	/**
	 * Gets the current dialogue info.
	 * @return PlayerDialogueInfo|null The current player dialogue info or null if none.
	 */
	public function getCurrentDialogueInfo() : ?PlayerDialogueInfo{
		$dialogue = $this->current_dialogue?->dialogue;
		return $dialogue !== NullDialogue::instance() ? $this->current_dialogue : null;
	}

	/**
	 * Gets the current dialogue.
	 * @return Dialogue|null The current dialogue or null if none.
	 */
	public function getCurrentDialogue() : ?Dialogue{
		return $this->getCurrentDialogueInfo()?->dialogue;
	}

	/**
	 * Handles the player's response to a dialogue.
	 * @param string $scene_name The scene name of the dialogue.
	 * @param int $index The index of the button clicked.
	 */
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

	/**
	 * Removes the current dialogue.
	 * @return PlayerDialogueInfo|null The removed player dialogue info or null if none.
	 */
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