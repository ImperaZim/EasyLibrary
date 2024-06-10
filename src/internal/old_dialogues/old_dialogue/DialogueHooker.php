<?php

declare(strict_types = 1);

namespace internal\dialogue;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\NpcRequestPacket;

use internal\dialogue\form\DialogueButton;

/**
* Class DialogueHooker
* @package internal\dialogue
*/
final class DialogueHooker implements Listener {
  
  /** @var array $dialogueQueue */
  public static array $dialogueQueue = [];

  /**
  * DialogueHooker constructor.
  * @param PluginBase|null $registrant
  */
  public function __construct(private ?PluginBase $registrant = null) {
    $this->register();
  }

  /**
  * Checks if the plugin is registered.
  * @return bool
  */
  public function isRegistered() : bool {
    return $this->registrant !== null && $this->registrant->isEnabled();
  }

  /**
  * Registers the hooker.
  * @return void
  */
  public function register(): void {
    if ($this->isRegistered()) {
      return;
    }
    $this->registrant->getServer()->getPluginManager()->registerEvents($this, $this->registrant);
  }

  /**
  * Handles incoming data packets.
  * @param DataPacketReceiveEvent $event
  * @throws AssumptionFailedError
  */
  public function onDataPacketReceive(DataPacketReceiveEvent $event) : void {
    $packet = $event->getPacket();
    if ($packet instanceof NpcRequestPacket) {
      $event->cancel();
      $requestType = $packet->requestType;
      $player = $event->getOrigin()->getPlayer() ?: throw new AssumptionFailedError("This packet cannot be received when player is not connected");
      $npcDialogue = DialogueHooker::$dialogueQueue[$player->getName()][$packet->sceneName] ?? null;
      if ($npcDialogue === null) {
        return;
      }
      if ($requestType === NpcRequestPacket::REQUEST_SET_ACTIONS) {
        $actionData = json_decode($packet->commandString, true, 512);
        if (!is_array($actionData)) {
          throw new AssumptionFailedError("Decoded json should be array");
        }
        $buttons = [];
        foreach ($actionData as $key => $actionDatum) {
          if (!is_array($actionDatum)) {
            throw new AssumptionFailedError("Action data should be array");
          }
          $button = DialogueButton::create()
          ->setName((string) $actionDatum["button_name"])
          ->setText((string) $actionDatum["text"])
          ->setMode((int) $actionDatum["mode"])
          ->setType((int) $actionDatum["type"]);
          $buttons[] = $button;
        }
        $npcDialogue->onButtonsChanged($buttons);
      } elseif ($requestType === NpcRequestPacket::REQUEST_EXECUTE_ACTION) {
        $buttonIndex = $packet->actionIndex;
        $npcDialogue->onButtonClicked($player, $buttonIndex);
      } elseif ($requestType === NpcRequestPacket::REQUEST_SET_NAME) {
        // Add relevant code for handling SET_NAME request
      } elseif ($requestType === NpcRequestPacket::REQUEST_SET_INTERACTION_TEXT) {
        // Add relevant code for handling SET_INTERACTION_TEXT request
      } elseif ($requestType === NpcRequestPacket::REQUEST_SET_SKIN) {
        // Add relevant code for handling SET_SKIN request
      }
    }
  }

  /**
  * Handles the event when a player quits.
  * @param PlayerQuitEvent $event
  */
  public function onPlayerQuit(PlayerQuitEvent $event) : void {
    $player = $event->getPlayer();
    if (isset(DialogueHooker::$dialogueQueue[$player->getName()])) {
      foreach (DialogueHooker::$dialogueQueue[$player->getName()] as $sceneName => $dialogue) {
        $dialogue->onClose($player);
      }
      unset(DialogueHooker::$dialogueQueue[$player->getName()]);
    }
  }
}