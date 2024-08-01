<?php

declare(strict_types = 1);

namespace imperazim\components\plugin\traits;

use Throwable;
use Exception;
use ZipArchive;
use ReflectionClass;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

use pocketmine\Server;
use pocketmine\resourcepacks\ZippedResourcePack;

use imperazim\components\filesystem\Path;

/**
* Trait PluginResourcePacksTrait
* @package imperazim\components\plugin\traits
*/
trait PluginResourcePacksTrait {
  
  /** @var string */
  protected string $sourceDirectory = 'resource_packs';
  /** @var string */
  protected string $targetDirectory = 'textures';

  /**
  * Sets the base directory for texture files.
  * @param string $source
  * @param string|null $target
  */
  public function registerTextures(string $source, ?string $target = 'textures'): void {
    $this->sourceDirectory = $source;
    $this->targetDirectory = $target ?? 'textures';
    $this->registerTexturesInternal();
  }

  /**
  * Setup all textures on /textures
  */
  private function registerTexturesInternal(): void {
    try {
      $source = rtrim($this->sourceDirectory, '/') . '/';
      if (!is_dir($source)) {
        throw new Exception("Source directory does not exist: {$source}");
      }

      $textures = array_filter(scandir($source), function ($item) use ($source) {
        return $item !== '.' && $item !== '..' && is_dir($source . $item);
      });

      $textureFolder = rtrim($this->targetDirectory, '/') . '/';
      $targetPath = new Path($textureFolder, false);
      $targetPath->deleteFolderRecursive();

      if (!file_exists($textureFolder) && !mkdir($textureFolder, 0777, true) && !is_dir($textureFolder)) {
        throw new Exception('Failed to create the destination folder.');
      }

      foreach ($textures as $name) {
        $this->processTexture($name, $source, $textureFolder);
      }
    } catch (Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Process and zip a texture folder.
  * @param string $name
  * @param string $source
  * @param string $textureFolder
  * @return void
  */
  private function processTexture(string $name, string $source, string $textureFolder): void {
    $zipFile = $textureFolder . $name . '.zip';
    $sourceFolder = $source . $name;

    if (file_exists($zipFile)) {
      unlink($zipFile);
    }

    if (file_exists($sourceFolder)) {
      $zip = new ZipArchive();
      if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        $files = new RecursiveIteratorIterator(
          new RecursiveDirectoryIterator($sourceFolder),
          RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
          if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($sourceFolder) + 1);
            $zip->addFile($filePath, $relativePath);
          }
        }
        $zip->close();

        $this->registerPack($zipFile);
      }
    }
  }

  /**
  * Register a zipped resource pack.
  * @param string $zipFile
  * @return void
  */
  private function registerPack(string $zipFile): void {
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
  }
}