<?php
declare(strict_types = 1);

namespace imperazim\vendor\customies;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\Experiments;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\network\mcpe\protocol\ItemComponentPacket;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\BiomeDefinitionListPacket;

use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\PluginComponent;
use imperazim\components\plugin\traits\PluginComponentsTrait;

use imperazim\vendor\customies\item\CustomiesItemFactory;
use imperazim\vendor\customies\block\CustomiesBlockFactory;

use function count;
use function array_merge;

/**
* Class CustomiesManager
* @package imperazim\vendor\customies
*/
final class CustomiesManager extends PluginComponent implements Listener {
  use PluginComponentsTrait;

  /** @var Experiments */
  private Experiments $experiments;
  /** @var ItemTypeEntry[] */
  private array $cachedItemTable = [];
  /** @var BlockPaletteEntry[] */
  private array $cachedBlockPalette = [];
  /** @var ItemComponentPacket|null */
  private ?ItemComponentPacket $cachedItemComponentPacket = null;

  /**
  * Initializes the customies component.
  * @param PluginToolkit $plugin The Plugin.
  */
  public static function init(PluginToolkit $plugin): array {
    $cachePath = $plugin->getDataFolder() . "idcache";
    $this->experiments = new Experiments([
      "data_driven_items" => true,
    ], true);

    self::setPlugin(plugin: $plugin);
    return [
      self::LISTENER_COMPONENT => [
        $this
      ],
      self::SCHEDULER_COMPONENT => [
        'type' => 'delayed',
        'class' => new ClosureTask(static function () use ($cachePath): void {
          CustomiesBlockFactory::getInstance()->addWorkerInitHook($cachePath);
        }),
        'sleep' => 0
      ]
    ];
  }

  public function onDataPacketSend(DataPacketSendEvent $event): void {
    foreach ($event->getPackets() as $packet) {
      if ($packet instanceof BiomeDefinitionListPacket) {
        // ItemComponentPacket needs to be sent after the BiomeDefinitionListPacket.
        if ($this->cachedItemComponentPacket === null) {
          // Wait for the data to be needed before it is actually cached. Allows for all blocks and items to be
          // registered before they are cached for the rest of the runtime.
          $this->cachedItemComponentPacket = ItemComponentPacket::create(CustomiesItemFactory::getInstance()->getItemComponentEntries());
        }
        foreach ($event->getTargets() as $session) {
          $session->sendDataPacket($this->cachedItemComponentPacket);
        }
      } elseif ($packet instanceof StartGamePacket) {
        if (count($this->cachedItemTable) === 0) {
          // Wait for the data to be needed before it is actually cached. Allows for all blocks and items to be
          // registered before they are cached for the rest of the runtime.
          $this->cachedItemTable = CustomiesItemFactory::getInstance()->getItemTableEntries();
          $this->cachedBlockPalette = CustomiesBlockFactory::getInstance()->getBlockPaletteEntries();
        }
        $packet->levelSettings->experiments = $this->experiments;
        $packet->itemTable = array_merge($packet->itemTable, $this->cachedItemTable);
        $packet->blockPalette = $this->cachedBlockPalette;
      } elseif ($packet instanceof ResourcePackStackPacket) {
        $packet->experiments = $this->experiments;
      }
    }
  }
}