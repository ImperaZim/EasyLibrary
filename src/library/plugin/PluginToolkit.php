<?php

declare(strict_types = 1);

namespace library\plugin;

use pocketmine\Server;
use library\filesystem\Path;
use library\filesystem\File;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginLoader;
use pocketmine\utils\SingletonTrait;
use pocketmine\plugin\ResourceProvider;
use pocketmine\plugin\PluginDescription;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Exception;

/**
* Class PluginToolkit
* @package library\plugin
*/
abstract class PluginToolkit extends PluginBase {
  use SingletonTrait;

  /** @var string|null */
  private ?string $environment = null;

  public function __construct(
    PluginLoader $loader,
    Server $server,
    PluginDescription $description,
    string $dataFolder,
    string $file,
    ResourceProvider $resourceProvider
  ) {
    parent::__construct($loader, $server, $description, $dataFolder, $file, $resourceProvider);
  }

  /**
  * Load all files in /resources plugin.
  * @param string|null $loadType
  * @return array|null
  */
  public function saveRecursiveResources(?string $loadType = '--merge'): ?array {
    if (is_dir($dir = $this->getResourcesDirectory())) {
      $loadedFiles = [];
      try {
        $files = Path::getRecursiveFiles($dir);
        foreach ($files as $file) {
          $fileName = $file['fileName'] ?? null;
          $fileType = File::match($file['fileType']);
          $fileContent = $file['content'] ?? null;
          $fileDirectory = $file['directory'] ?? null;

          if ($fileName !== null && $fileType !== null && $fileContent !== null && $fileDirectory !== null) {
            $loadedFiles[] = new File(
              directoryOrConfig: $fileDirectory,
              fileName: $fileName,
              fileType: File::match($fileType),
              autoGenerate: true,
              readCommand: [$loadedFiles => $fileContent]
            );
          }
        }
      } catch (Exception $e) {
        $this->getLogger()->error("Erro ao carregar recursos: " . $e->getMessage());
        return null;
      }
      return $loadedFiles;
    }
    return null;
  }

  /**
  * Gets the plugin resources path.
  * @return string The plugin resource path.
  */
  public function getResourcesDirectory(): string {
    return $this->file . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR;
  }

  /**
  * Gets the data path of the server.
  * @param array|null $join Continue paths.
  * @return string The server data path.
  */
  public function getServerPath(?array $join = null): string {
    $path = $this->server->getDataPath();
    if ($join !== null) {
      if (strtolower($join[0]) === 'join:data') {
        $path .= $join[0] . DIRECTORY_SEPARATOR . $this->getName();
      } else {
        $path .= rtrim(implode(DIRECTORY_SEPARATOR, $join));
      }
    }
    return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
  }
  
}