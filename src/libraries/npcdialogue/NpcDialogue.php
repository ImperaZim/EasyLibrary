<?php

declare(strict_types = 1);

namespace libraries\npcdialogue;

use pocketmine\utils\Utils;
use pocketmine\entity\Human;
use pocketmine\entity\Entity;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\NpcDialoguePacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\network\mcpe\protocol\types\entity\ByteMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\StringMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

use player\Player;
use libraries\npcdialogue\form\NpcDialogueButtonData;
use libraries\npcdialogue\event\DialogueNameChangeEvent;

/**
* Class NpcDialogue
* @package libraries\npcdialogue
*/
final class NpcDialogue {

  /** @var int|null $actorId */
  protected ?int $actorId = null;

  /** @var bool $fakeActor */
  protected bool $fakeActor = true;

  /** @var array $buttonData */
  protected array $buttonData = [];

  /** @var string $sceneName */
  protected string $sceneName = "";

  /** @var string $npcName */
  protected string $npcName = "";

  /** @var string $dialogueBody */
  protected string $dialogueBody = "";

  /** @var int $pickerOffset */
  private int $pickerOffset = -50;

  /**
  * Set the scene name for the dialogue.
  * @param string $sceneName
  * @throws \InvalidArgumentException
  */
  public function setSceneName(string $sceneName) : void {
    if (trim($sceneName) === "") {
      throw new \InvalidArgumentException("Scene name cannot be empty");
    }
    $this->sceneName = $sceneName;
  }

  /**
  * Set the NPC name for the dialogue.
  * @param string $npcName
  */
  public function setNpcName(string $npcName) : void {
    $this->npcName = $npcName;
  }

  /**
  * Set the dialogue body for the NPC.
  * @param string $dialogueBody
  */
  public function setDialogueBody(string $dialogueBody) : void {
    $this->dialogueBody = $dialogueBody;
  }

  /**
  * Send the dialogue to a player.
  * @param Player $player
  * @param Entity|null $entity
  * @throws AssumptionFailedError
  */
  public function sendTo(Player $player, ?Entity $entity = null) : void {
    if (trim($this->sceneName) === "") {
      throw new \InvalidArgumentException("Scene name cannot be empty");
    }
    $mappedActions = Utils::assumeNotFalse(json_encode(array_map(static fn(NpcDialogueButtonData $data) => $data->jsonSerialize(), $this->buttonData)));
    $skinIndex = [
      "picker_offsets" => [
        "scale" => [0,
          0,
          0],
        "translate" => [0,
          0,
          0],
      ],
      "portrait_offsets" => [
        "scale" => [1,
          1,
          1],
        "translate" => [0,
          $this->pickerOffset,
          0]
      ]
    ];
    if ($entity === null) {
      $this->actorId = Entity::nextRuntimeId();
      $player->getNetworkSession()->sendDataPacket(
        AddActorPacket::create(
          $this->actorId,
          $this->actorId,
          EntityIds::NPC,
          $player->getPosition()->add(0, 10, 0),
          null,
          $player->getLocation()->getPitch(),
          $player->Location()->getYaw(),
          $player->getLocation()->getYaw(),
          $player->getLocation()->getYaw(),
          [],
          [
            EntityMetadataProperties::HAS_NPC_COMPONENT => new ByteMetadataProperty(1),
            EntityMetadataProperties::INTERACTIVE_TAG => new StringMetadataProperty($this->dialogueBody),
            EntityMetadataProperties::NPC_ACTIONS => new StringMetadataProperty($mappedActions),
          ],
          new PropertySyncData([], []),
          []
        )
      );
    } else {
      $this->actorId = $entity->getId();
      $this->fakeActor = false;
      $propertyManager = $entity->getNetworkProperties();
      $propertyManager->setByte(EntityMetadataProperties::HAS_NPC_COMPONENT, 1);
      $propertyManager->setString(EntityMetadataProperties::INTERACTIVE_TAG, $this->dialogueBody);
      $propertyManager->setString(EntityMetadataProperties::NPC_ACTIONS, $mappedActions);
      if ($entity instanceof Human) {
        $propertyManager->setString(EntityMetadataProperties::NPC_SKIN_INDEX, Utils::assumeNotFalse(json_encode($skinIndex)));
      }
    }
    $pk = NpcDialoguePacket::create(
      $this->actorId,
      NpcDialoguePacket::ACTION_OPEN,
      $this->dialogueBody,
      $this->sceneName,
      $this->npcName,
      $mappedActions
    );
    $player->getNetworkSession()->sendDataPacket($pk);

    DialogueStore::$dialogueQueue[$player->getName()][$this->sceneName] = $this;
  }

  /**
  * Event triggered when buttons are changed.
  * @param array $buttons
  */
  public function onButtonsChanged(array $buttons) : void {}

  /**
  * Event triggered when the dialogue is closed.
  * @param Player $player
  */
  public function onClose(Player $player) : void {
    $mappedActions = Utils::assumeNotFalse(json_encode(array_map(static fn(NpcDialogueButtonData $data) => $data->jsonSerialize(), $this->buttonData)));
    $player->getNetworkSession()->sendDataPacket(
      NpcDialoguePacket::create(
        $this->actorId ?? throw new AssumptionFailedError("This method should not be called when actorId is null"),
        NpcDialoguePacket::ACTION_CLOSE,
        $this->dialogueBody,
        $this->sceneName,
        $this->npcName,
        $mappedActions
      )
    );
  }

  /**
  * Event triggered when a button is clicked.
  * @param Player $player
  * @param int $buttonId
  */
  public function onButtonClicked(Player $player, int $buttonId) : void {
    if (!array_key_exists($buttonId, $this->buttonData)) {
      throw new \InvalidArgumentException("Button ID $buttonId does not exist");
    }
    $button = $this->buttonData[$buttonId];

    if ($button->getForceCloseOnClick()) {
      $this->onClose($player);
    }

    $handler = $button->getClickHandler();
    if ($handler !== null) {
      $handler($player);
    }
  }

  /**
  * Event triggered when the NPC name is requested to be changed.
  * @param string $newName
  */
  public function onSetNameRequested(string $newName) : void {
    $ev = new DialogueNameChangeEvent($this, $this->npcName, $newName);
    $ev->call();
    if ($ev->isCancelled()) {
      return;
    }
    $this->npcName = $ev->getNewName();
  }

  /**
  * Add a button to the dialogue.
  * @param NpcDialogueButtonData $buttonData
  */
  public function addButton(NpcDialogueButtonData $buttonData) : void {
    $this->buttonData[] = $buttonData;
  }

  /**
  * Event triggered when the dialogue is disposed.
  * @param Player $player
  */
  public function onDispose(Player $player) : void {
    if ($this->actorId !== null && $this->fakeActor) {
      $player->getNetworkSession()->sendDataPacket(RemoveActorPacket::create($this->actorId));
      $this->actorId = null;
    }
  }

  /**
  * Set the picker offset.
  * @param int $offset
  */
  public function setPickerOffset(int $offset) : void {
    $this->pickerOffset = $offset;
  }
}