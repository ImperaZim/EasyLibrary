<?php

declare(strict_types = 1);

use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\traits\PluginToolkitTrait;

/**
* Class Library
* TODO: This class should not be called in other plugins!
*/
final class Library extends PluginToolkit {
  use PluginToolkitTrait;

  private LibraryComponents $componentsManager;

  /**
  * This method is called when the plugin is enabled.
  */
  protected function onEnable(): void {
    $this->componentsManager = new LibraryComponents($this);
    $this->componentsManager->enableComponents();
  }

  /**
  * This method is called when the plugin is disabled.
  */
  protected function onDisable(): void {
    $customiesEnchantment = $this->componentsManager->componentsFile->get('vendor.CustomiesEnchantment', false);
    $this->componentsManager->disableCustomiesEnchantment($customiesEnchantment);
  }
}