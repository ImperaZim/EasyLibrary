<?php

declare(strict_types = 1);

namespace library\plugin;

use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase;

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

  /**
  * Gets the data path of the server.
  * @param array|null $join Continue paths.
  * @return string The serve data path.
  */
  public function getServerPath(?array $join = null): string {
    $path = Server::getInstance()->getDataPath();
    if ($join !== null) {
      if (strtolower($join[0]) === 'join:data') {
        $path .= $join[0] . DIRECTORY_SEPARATOR . $this->getName();
      } else {
        $path .= rtrim(implode(DIRECTORY_SEPARATOR, $join));
      }
    }
    return trim($path . DIRECTORY_SEPARATOR);
  }

}