<?php

declare(strict_types = 1);

namespace imperazim\components\utils;

use Exception;
use ReflectionClass;
use pocketmine\Server;
use pocketmine\resourcepacks\ZippedResourcePack;
use imperazim\components\plugin\exception\PluginException;

/**
* Class ResourcePacks
* @package imperazim\components\utils
*/
final class ResourcePacks {

  /**
  * Registers a zipped resource pack with the resource pack manager using reflection.
  * @param string $zipFile The path to the zipped resource pack.
  * @return void
  * @throws Exception If there is an issue registering the resource pack.
  */
  public static function registerPack(string $zipFile): void {
    try {
      $pack = new ZippedResourcePack($zipFile);
      $manager = Server::getInstance()->getResourcePackManager();

      $reflection = new ReflectionClass($manager);
      $resourcePacksProperty = $reflection->getProperty('resourcePacks');
      $resourcePacks = $resourcePacksProperty->getValue($manager);
      $resourcePacks[] = $pack;
      $resourcePacksProperty->setValue($manager, $resourcePacks);

      $uuidListProperty = $reflection->getProperty('uuidList');
      $uuidList = $uuidListProperty->getValue($manager);
      $uuidList[strtolower($pack->getPackId())] = $pack;
      $uuidListProperty->setValue($manager, $uuidList);

      $serverForceResourcesProperty = $reflection->getProperty('serverForceResources');
      $serverForceResourcesProperty->setValue($manager, true);
    } catch (PluginException $e) {
      new \crashdump($e);
    }
  }

}