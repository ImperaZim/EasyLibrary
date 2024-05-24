<?php

declare(strict_types = 1);

namespace library\plugin;

use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase;
use Symfony\Component\Filesystem\Path;

/**
* Class PluginToolkit
* @package library\plugin
*/
abstract class PluginToolkit extends PluginBase {
  private ?string $environment = null;

  /**
  * Sets the environment for the plugin (e.g., 'production', 'development').
  * @param string $environment The environment to set.
  * @return void
  */
  public function setEnvironment(string $environment): void {
    $this->environment = strtolower(trim($environment));
  }

  /**
  * Gets the current environment for the plugin.
  * @return string|null The current environment, or null if not set.
  */
  public function getEnvironment(): ?string {
    return $this->environment;
  }

}