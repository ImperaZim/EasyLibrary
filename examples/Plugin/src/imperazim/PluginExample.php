<?php

declare(strict_types = 1);

namespace imperazim;

use imperazim\ui\UiManager;
use imperazim\hud\HudManager;

use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\traits\PluginToolkitTrait;

/**
* Class PluginExample
* @package imperazim
*/
final class PluginExample extends PluginToolkit {
  use PluginToolkitTrait;

  /**
  * This method is called when the plugin is enabled.
  */
  protected function onEnable(): void {
    /**
    * Recursively loads all files (YAML, YML, JSON, TXT and INI) in the resources folder.
    */
    $this->saveRecursiveResources();

    /**
    * Adds and initializes the component passed in functions!
    *
    * IMPORTANT: Remember that you need to follow the structure rules of a component class, look at the plugin's component classes to get a more in-depth idea of how to use it!
    */
    $this->addComponent($this, UiManager::class);
    $this->addComponent($this, HudManager::class);
  }
}