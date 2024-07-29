<?php

declare(strict_types = 1);

namespace imperazim\item;

use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\PluginComponent;
use imperazim\components\plugin\traits\PluginComponentsTrait;

use imperazim\vendor\customies\item\CustomiesItemFactory;

/**
* Class ItemManager
* @package imperazim\item
*/
final class ItemManager extends PluginComponent {
  use PluginComponentsTrait;

  /**
  * Initializes the item component.
  * @param PluginToolkit $plugin The Plugin.
  */
  public static function init(PluginToolkit $plugin): array {
    /**
    * Define the main instance to make the code easier and there is no need to define getInstance in main if you are adding it just for that! However, it is not mandatory.
    */
    self::setPlugin(plugin: $plugin);

    /**
    * To register a custom item, you must pass the custom item class, an identifier (identifier and the id referring to the item, for example "diamond_sword") and a name in the register arguments.
    *
    * IMPORTANT: In the code below it obtains the IDENTIFIER and NAME that I defined in the item class itself, but you can just write them in registerItem.
    */
    $items = [
      Ruby::class
    ];
    foreach ($items as $item) {
      $factory = CustomiesItemFactory::getInstance();
      $factory->registerItem(
        className: $item,
        identifier: $item::IDENTIFIER,
        name: $item::NAME
      );
    }

    /**
    * Registers the subcomponents of the current component.
    * View on ComponentTypes [COMMAND, LISTENER, SCHEDULER, NETWORK]
    */
    return [];
  }

}