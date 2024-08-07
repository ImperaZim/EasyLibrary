<?php

declare(strict_types = 1);

namespace imperazim\components\world;

use Library;
use imperazim\components\filesystem\Path;
use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\PluginComponent;
use imperazim\components\world\exception\WorldException;
use imperazim\components\plugin\traits\PluginComponentsTrait;

use pocketmine\Server;
use pocketmine\world\World;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\WorldManager as IWorldManager;
use pocketmine\world\format\io\data\BaseNbtWorldData;

/**
* Class WorldManager
* @package imperazim\components\world
*/
final class WorldManager extends PluginComponent {
  use PluginComponentsTrait;

  /** @var IWorldManager */
  private static IWorldManager $worldManager;

  /**
  * Initializes the season component.
  * @param PluginToolkit $plugin The Plugin.
  */
  public static function init(PluginToolkit $plugin): array {
    $generatorsDir = __DIR__ . '/generators';
    foreach (scandir($generatorsDir) as $file) {
      if (is_file($generatorsDir . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        require_once $generatorsDir . '/' . $file;
        $className = pathinfo($file, PATHINFO_FILENAME);
        if (class_exists("generators\\$className")) {
          GeneratorManager::getInstance()->addGenerator("generators\\$className", $className, fn() => null, true);
        }
      }
    }

    self::$worldManager = $plugin->getServer()->getWorldManager();
    self::setPlugin(plugin: $plugin);
    return [];
  }

  /**
  * Get the path to the world directory.
  * @return string
  */
  public static function getWorldPath(string $world): string {
    return self::getPlugin()->getServerPath(join: ['worlds', $world]);
  }

  /**
  * Check if a world exists.
  * @param string $name The name of the world.
  * @return bool
  */
  public static function worldExists(string $name): bool {
    return is_dir(self::getWorldPath($name));
  }

  /**
  * Get a world by its name.
  * @param string $name The name of the world.
  * @return World|null
  */
  public static function getWorld(string $name): ?World {
    try {
      self::load($name);
      return self::$worldManager->getWorldByName($name);
    } catch (WorldException $e) {
      new \crashdump($e);
      return null;
    }
  }

  /**
  * Get all worlds.
  * @return World[]
  */
  public static function getWorlds(): array {
    try {
      return self::$worldManager->getWorlds();
    } catch (WorldException $e) {
      new \crashdump($e);
      return [];
    }
  }

  /**
  * Get the default world.
  * @return World|null
  */
  public static function getDefaultWorld(): ?World {
    try {
      return self::$worldManager->getDefaultWorld();
    } catch (WorldException $e) {
      new \crashdump($e);
      return null;
    }
  }

  /**
  * Rename a world.
  * @param string $old The old name of the world.
  * @param string $new The new name of the world.
  * @throws WorldException If the world does not exist.
  */
  public static function renameWorldName(string $old, string $new): void {
    if (!self::worldExists($old)) {
      throw new WorldException("World '$old' does not exist.");
    }
    try {
      self::unload($old);
      rename(self::getWorldPath($old), self::getWorldPath($new));
      self::load($new);
      $newWorld = self::getWorld($new);
      if ($newWorld instanceof World) {
        $worldData = $newWorld->getProvider()->getWorldData();
        if ($worldData instanceof BaseNbtWorldData) {
          $worldData->getCompoundTag()->setString("LevelName", $new);
          self::unload($new);
          self::load($new);
        }
      }
    } catch (WorldException $e) {
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
      $manager = self::$worldManager;
      if (!$manager->isWorldLoaded($name)) {
        return $manager->loadWorld($name, true);
      }
      return true;
    } catch (WorldException $e) {
      new \crashdump($e);
      return false;
    }
  }

  /**
  * Unload a world by its name.
  * @param string $name The name of the world.
  * @return bool
  */
  public static function unload(string $name): bool {
    try {
      $world = self::getWorld($name);
      if ($world === null) {
        return false;
      }
      return self::$worldManager->unloadWorld($world, false);
    } catch (WorldException $e) {
      new \crashdump($e);
      return false;
    }
  }

  /**
  * Create a new world.
  * @param string $world The name of the world.
  * @param string $generator The generator of the world.
  * @param mixed $seed The seed of the world.
  * @return bool
  */
  public static function createWorld(string $world, string $generator, mixed $seed): bool {
    try {
      self::$worldManager->generateWorld(
        $world,
        WorldCreationOptions::create()
        ->setSeed($seed)
        ->setGeneratorClass(
          GeneratorManager::getInstance()
          ->getGenerator($generator)
          ->getGeneratorClass()
        )
      );
      return true;
    } catch (WorldException $e) {
      new \crashdump($e);
      return false;
    }
  }

  /**
  * Duplicate an existing world.
  * @param string $oldName The old name of the world.
  * @param string $newWorld The new name of the world.
  * @throws WorldException If the old world does not exist.
  */
  public static function duplicateWorld(string $oldName, string $newWorld): void {
    if (!self::worldExists($oldName)) {
      throw new WorldException("World '$oldName' does not exist.");
    }
    try {
      $worldPathOld = new Path(self::getWorldPath($oldName));
      $worldPathNew = new Path(self::getWorldPath($newWorld), true);
      $worldPathOld->copyFolderTo($worldPathNew);

      self::renameWorldName($newWorld, $newWorld);
    } catch (WorldException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Backup a world.
  * @param string $name The name of the world.
  * @return bool
  */
  public static function backupWorld(string $name): bool {
    try {
      if (self::worldExists($name)) {
        $backupName = $name . '_backup_' . date('Ymd_His');
        self::duplicateWorld($name, $backupName);
        return true;
      }
    } catch (WorldException $e) {
      new \crashdump($e);
    }
    return false;
  }

  /**
  * Restore a world from a backup.
  * @param string $name The name of the world.
  * @param string $backupPath The path to the backup.
  * @return bool
  */
  public static function restoreWorld(string $name, string $backupPath): bool {
    try {
      $worldPath = self::getWorldPath($name);
      if (is_dir($backupPath)) {
        self::unload($name);
        $worldPathOld = new Path($worldPath);
        $worldPathOld->deleteFolderRecursive();
        self::duplicateWorld($backupPath, $worldPath);
        self::load($name);
        return true;
      }
    } catch (WorldException $e) {
      new \crashdump($e);
    }
    return false;
  }

  /**
  * Delete a world.
  * @param string $name The name of the world.
  * @return bool
  */
  public static function deleteWorld(string $name): bool {
    try {
      if (self::worldExists($name)) {
        self::unload($name);
        $worldPath = new Path(self::getWorldPath($name));
        $worldPath->deleteFolderRecursive();
        return true;
      }
    } catch (WorldException $e) {
      new \crashdump($e);
    }
    return false;
  }

}