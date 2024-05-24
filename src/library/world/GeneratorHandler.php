<?php

declare(strict_types = 1);

namespace library\world;

use pocketmine\plugin\PluginBase;
use pocketmine\world\generator\GeneratorManager;

/**
* Class GeneratorHandler
* @package library\world
*/
final class GeneratorHandler {
  
  /** @var string */
  protected string $generators = __DIR__ . '/generators';

  /**
  * GeneratorHandler constructor.
  * @param PluginBase|null $plugin
  */
  public function __construct(private ?PluginBase $plugin = null) {
    foreach (scandir($this->generators) as $file) {
      if (is_file($this->generators . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        require_once $this->generators . '/' . $file;
        $className = pathinfo($file, PATHINFO_FILENAME);
        if (class_exists("generators\\$className")) {
          GeneratorManager::getInstance()->addGenerator("generators\\$className", $className, fn() => null, true);
        }
      }
    }
  }


}