<?php

declare(strict_types = 1);

namespace imperazim\item\enchantment;

use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\PluginComponent;
use imperazim\components\plugin\traits\PluginComponentsTrait;

use imperazim\vendor\customies\enchantment\enchants\CustomEnchant;
use imperazim\vendor\customies\enchantment\CustomiesEnchantmentFactory;

/**
* Class EnchantmentManager
* @package imperazim\item\enchantment
*/
final class EnchantmentManager extends PluginComponent {
  use PluginComponentsTrait;

  /**
  * Initializes the enchantment component.
  * @param PluginToolkit $plugin The Plugin.
  */
  public static function init(PluginToolkit $plugin): array {
    /**
    * Define the main instance to make the code easier and there is no need to define getInstance in main if you are adding it just for that! However, it is not mandatory.
    */
    self::setPlugin(plugin: $plugin);
    
    $enchantments = [
      new EnergizingEnchant()
    ];
    foreach ($enchantments as $enchantment) {
      CustomiesEnchantmentFactory::registerEnchantment(
        enchant: $enchantment
      );
    }

    /**
    * Registers the subcomponents of the current component.
    * View on ComponentTypes [COMMAND, LISTENER, SCHEDULER, NETWORK]
    */
    return [];
  }

  /**
  * Returns all registered custom enchantments.
  * @return CustomEnchant[]
  */
  public static function getEnchantments(): array {
    return CustomiesEnchantmentFactory::getEnchantments();
  }

  /**
  * Returns a custom enchantment by its ID.
  * @param int $id
  * @return CustomEnchant|null
  */
  public static function getEnchantment(int $id): ?CustomEnchant {
    return CustomiesEnchantmentFactory::getEnchantment($id);
  }

  /**
  * Returns a custom enchantment by its name.
  * @param string $name
  * @return CustomEnchant|null
  */
  public static function getEnchantmentByName(string $name): ?CustomEnchant {
    return CustomiesEnchantmentFactory::getEnchantmentByName($name);
  }

}