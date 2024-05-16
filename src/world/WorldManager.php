<?php

declare(strict_types = 1);

namespace world;

use pocketmine\Server;
use libraries\utils\Path;
use pocketmine\world\World;
use world\generators\VoidGenerator;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\format\io\data\BaseNbtWorldData;

/**
* Class WorldManager
* @package world
*/
final class WorldManager {

  /**
  * Get the path to the worlds directory.
  * @return string
  */
  public static function getWorldsPath(): string {
    return Server::getInstance()->getDataPath() . '/worlds/';
  }

  /**
  * Get a world by its name.
  * @param string $name The name of the world.
  * @return World|null
  */
  public static function getWorld(string $name): ?World {
    try {
      WorldManager::load($name);
      $server = Server::getInstance();
      return $server->getWorldManager()->getWorldByName($name);
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
    return null;
  }

  /**
  * Get the default world.
  * @return World|null
  */
  public static function getDefaultWorld(): ?World {
    try {
      $server = Server::getInstance();
      return $server->getWorldManager()->getDefaultWorld();
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
    return null;
  }

  /**
  * Rename a world.
  * @param string $old The old name of the world.
  * @param string $new The new name of the world.
  */
  public static function renameWorldName(string $old, string $new): void {
    try {
      WorldManager::unload($old);
      rename(WorldManager::getPath() . $old, WorldManager::getPath() . $new);
      WorldManager::load($new);
      $newWorld = WorldManager::getWorld($new);
      if ($newWorld instanceof World) {
        $worldData = $newWorld->getProvider()->getWorldData();
        if ($worldData instanceof BaseNbtWorldData) {
          $worldData->getCompoundTag()->setString("LevelName", $new);
          $worldData->getCompoundTag()->setString("LevelType", "island");
          WorldManager::unload($new);
          WorldManager::load($new);
        }
      }
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Load a world by its name.
  * @param string $name The name of the world.
  * @return bool
  */
  public static function load(string $name): bool {
    try {
      $manager = Server::getInstance()->getWorldManager();
      return !$manager->isWorldLoaded($name) && $manager->loadWorld($name, true);
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
    return false;
  }

  /**
  * Unload a world by its name.
  * @param string $name The name of the world.
  * @return bool
  */
  public static function unload(string $name): bool {
    try {
      $manager = Server::getInstance()->getWorldManager();
      if (($world = $manager->getWorldByName($name)) !== null) {
        return $manager->unloadWorld($world, false);
      }
      return false;
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
    return false;
  }

  /**
  * Create a new world.
  */
  public static function createWorld(string $world, string $generator, mixed $seed): void {
    try {
      Server::getInstance()->getWorldManager()->generateWorld(
        $world,
        WorldCreationOptions::create()
        ->setSeed($seed)
        ->setGeneratorClass(
          GeneratorManager::getInstance()
          ->getGenerator($generator)
          ->getGeneratorClass()
        )
      );
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Duplicate a exist world
  */
  public static function DuplicateWorld(string $oldName, string $newWorld): void {
    try {
      $oldNamePath = WorldManager::getWorldsPath() . $oldName;
      $newNamePath = WorldManager::getWorldsPath() . $newWorld;

      Path::paste($newNamePath, $newNamePath);
      WorldManager::renameWorldName($oldName, $newWorld);
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Register custom world generators.
  */
  public static function registerGenerators(): void {
    try {
      GeneratorManager::getInstance()->addGenerator(VoidGenerator::class, 'void', fn() => null, true);
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

}