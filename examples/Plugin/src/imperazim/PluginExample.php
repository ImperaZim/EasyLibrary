<?php

declare(strict_types = 1);

namespace imperazim;

use imperazim\ui\UiManager;
use imperazim\hud\HudManager;
use imperazim\item\ItemManager;
use imperazim\item\enchantment\EnchantmentManager;

use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\traits\PluginToolkitTrait;
use imperazim\components\plugin\traits\PluginResourcePacksTrait;

/**
* Class PluginExample
* @package imperazim
*/
final class PluginExample extends PluginToolkit {
  use PluginToolkitTrait;
  use PluginResourcePacksTrait;

  /**
  * This method is called when the plugin is enabled.
  */
  protected function onEnable(): void {
    /**
    * Recursively loads all files (YAML, YML, JSON, TXT and INI) in the resources folder.
    */
    $this->saveRecursiveResources();

    /**
    * Register the textures according to the directory based on them, in the example below it passes the directory ./item/textures, therefore, all the textures in that directory will be registered on the server.
    */
    $this->registerTextures(__DIR__ . 'item/textures/', 'textures');

    /**
    * Adds and initializes the component passed in functions!
    *
    * IMPORTANT: Remember that you need to follow the structure rules of a component class, look at the plugin's component classes to get a more in-depth idea of how to use it!
    */
    $this->addComponent($this, UiManager::class);
    $this->addComponent($this, HudManager::class);
    $this->addComponent($this, ItemManager::class);
    $this->addComponent($this, EnchantmentManager::class);
  }
}