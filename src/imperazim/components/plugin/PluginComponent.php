<?php

declare(strict_types = 1);

namespace imperazim\components\plugin;

/**
* Class PluginComponent
* @package imperazim\components\plugin
*/
abstract class PluginComponent {

  /**
  * Init function that must be implemented by child classes.
  * @return array
  */
  abstract public static function init(PluginToolkit $plugin): array;
}