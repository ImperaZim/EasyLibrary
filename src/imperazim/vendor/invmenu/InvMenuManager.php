<?php

declare(strict_types = 1);

namespace imperazim\vendor\invmenu;

use LogicException;
use InvalidArgumentException;

use pocketmine\Server;
use pocketmine\plugin\Plugin;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\network\mcpe\protocol\NetworkStackLatencyPacket;

use imperazim\vendor\invmenu\session\PlayerManager;
use imperazim\vendor\invmenu\type\InvMenuTypeRegistry;
use imperazim\vendor\invmenu\session\network\PlayerNetwork;

use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\PluginComponent;
use imperazim\components\plugin\traits\PluginComponentsTrait;

/**
* Class InvMenuManager
* @package imperazim\vendor\invmenu
*/
final class InvMenuManager extends PluginComponent implements Listener {
  use PluginComponentsTrait;

  /** @var PlayerManager */
  private static PlayerManager $player_manager;
  /** @var InvMenuTypeRegistry */
  private static InvMenuTypeRegistry $type_registry;

  /**
  * Initializes the invmenu component.
  * @param PluginToolkit $plugin The Plugin.
  */
  public static function init(PluginToolkit $plugin): array {
    self::$type_registry = new InvMenuTypeRegistry();
    self::$player_manager = new PlayerManager($plugin);

    self::setPlugin(plugin: $plugin);
    return [
      self::LISTENER_COMPONENT => [
        $this
      ]
    ];
  }

  public static function getPlayerManager() : PlayerManager {
    return self::$player_manager;
  }

  public static function getTypeRegistry() : InvMenuTypeRegistry {
    return self::$type_registry;
  }
  
  /**
	 * @param DataPacketReceiveEvent $event
	 * @priority NORMAL
	 */
	public function onDataPacketReceive(DataPacketReceiveEvent $event) : void{
		$packet = $event->getPacket();
		if($packet instanceof NetworkStackLatencyPacket){
			$player = $event->getOrigin()->getPlayer();
			if($player !== null){
				self::player_manager->getNullable($player)?->network->notify($packet->timestamp);
			}
		}
	}

	/**
	 * @param InventoryCloseEvent $event
	 * @priority MONITOR
	 */
	public function onInventoryClose(InventoryCloseEvent $event) : void{
		$player = $event->getPlayer();
		$session = self::player_manager->getNullable($player);
		if($session === null){
			return;
		}

		$current = $session->getCurrent();
		if($current !== null && $event->getInventory() === $current->menu->getInventory()){
			$current->menu->onClose($player);
		}
		$session->network->waitUntil(PlayerNetwork::DELAY_TYPE_ANIMATION_WAIT, 325, static fn(bool $success) : bool => false);
	}

	/**
	 * @param InventoryTransactionEvent $event
	 * @priority NORMAL
	 */
	public function onInventoryTransaction(InventoryTransactionEvent $event) : void{
		$transaction = $event->getTransaction();
		$player = $transaction->getSource();

		$player_instance = self::player_manager->get($player);
		$current = $player_instance->getCurrent();
		if($current === null){
			return;
		}

		$inventory = $current->menu->getInventory();
		$network_stack_callbacks = [];
		foreach($transaction->getActions() as $action){
			if(!($action instanceof SlotChangeAction) || $action->getInventory() !== $inventory){
				continue;
			}

			$result = $current->menu->handleInventoryTransaction($player, $action->getSourceItem(), $action->getTargetItem(), $action, $transaction);
			$network_stack_callback = $result->post_transaction_callback;
			if($network_stack_callback !== null){
				$network_stack_callbacks[] = $network_stack_callback;
			}
			if($result->cancelled){
				$event->cancel();
				break;
			}
		}

		if(count($network_stack_callbacks) > 0){
			$player_instance->network->wait(PlayerNetwork::DELAY_TYPE_ANIMATION_WAIT, static function(bool $success) use($player, $network_stack_callbacks) : bool{
				if($success){
					foreach($network_stack_callbacks as $callback){
						$callback($player);
					}
				}
				return false;
			});
		}
	}
	
}