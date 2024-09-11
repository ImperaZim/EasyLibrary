<?php

declare(strict_types = 1);

namespace imperazim\components\plugin\traits;

use Throwable;
use Exception;
use ReflectionClass;
use pocketmine\Server;
use pocketmine\scheduler\ClosureTask;
use pocketmine\resourcepacks\ZippedResourcePack;

use Symfony\Component\Filesystem\Path;
use imperazim\components\utils\ResourcePacks;
use imperazim\components\plugin\exception\PluginException;

/**
* Trait PluginCdnResourcePackTrait
* @package imperazim\components\plugin\traits
*/
trait PluginCdnResourcePackTrait {

  /** @var string[] CDN URLs mapped by resource pack UUID and version */
  private array $cdnUrls = [];

  /** @var string CDN base URL */
  private string $sourceUrl;

  /** @var string CDN base directory */
  private string $targetDirectory;

  /** @var bool Whether or not to remove the file extension from CDN URLs */
  private bool $removeExtension;

  /**
  * Setup the CDN base URL and directory for resource packs.
  * @param string $source The base URL for the CDN.
  * @param string|null $target The base directory where resource packs are stored.
  * @param bool $removeExtension Whether or not to remove the file extension from the CDN URL.
  * @return void
  */
  public function registerCdnTextures(string $source, ?string $target = 'textures', bool $removeExtension = false): void {
    $this->sourceUrl = rtrim($source, '/') . '/';
    $this->targetDirectory = rtrim($target, '/') . '/';
    $this->removeExtension = $removeExtension;

    $this->scheduleCdnMatchingTask();
  }

  /**
  * Schedules a delayed task to match resource packs in the CDN directory.
  * @return void
  */
  private function scheduleCdnMatchingTask(): void {
    $this->matchPackInCdnDir();
    Server::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function (): void {
      $this->matchPackInCdnDir();
    }), 1);
  }

  /**
  * Matches the resource packs in the CDN directory and maps them to CDN URLs.
  * @return void
  * @throws Exception If there is an issue processing the resource packs.
  */
  private function matchPackInCdnDir(): void {
    try {
      $resourcePackManager = Server::getInstance()->getResourcePackManager();
      foreach ($resourcePackManager->getResourceStack() as $pack) {
        if (
          $pack instanceof ZippedResourcePack &&
          Path::isBasePath($this->targetDirectory, $pack->getPath())
        ) {
          $uuid = $pack->getPackId();
          $version = $pack->getPackVersion();
          $key = $uuid . "_" . $version;

          if (!isset($this->cdnUrls[$key])) {
            $cdnUrl = $this->sourceUrl . Path::makeRelative($pack->getPath(), $this->targetDirectory);
            if ($this->removeExtension) {
              $cdnUrl = substr($cdnUrl, 0, -strlen(strrchr($cdnUrl, ".")));
            }
            $this->cdnUrls[$key] = $cdnUrl;
            ResourcePacks::registerPack($pack->getPath());
          }
        }
      }
    } catch (PluginException $e) {
      new \crashdump($e);
    }
  }
  
}