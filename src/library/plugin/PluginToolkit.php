<?php

declare(strict_types = 1);

namespace library\plugin;

use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginLoader;
use pocketmine\plugin\ResourceProvider;
use pocketmine\plugin\PluginDescription;

use library\filesystem\Path;
use library\filesystem\File;
use library\database\DatabaseManager;
use library\plugin\exception\PluginException;

/**
* Class PluginToolkit
* @package library\plugin
*/
abstract class PluginToolkit extends PluginBase {

  /** @var array */
  private array $database;
  
  /** @var mysqli */
  private mysqli $connection;

  public function __construct(
    private PluginLoader $loader,
    private Server $server,
    private PluginDescription $description,
    private string $dataFolder,
    private string $file,
    private ResourceProvider $resourceProvider
  ) {
    parent::__construct($loader, $server, $description, $dataFolder, $file, $resourceProvider);
  }

  /**
  * Register multiple commands.
  * @param Command[] $commands An array of Command instances to register.
  * @return void
  */
  public function registerCommands(array $commands): void {
    $commandMap = $this->getServer()->getCommandMap();
    foreach ($commands as $command) {
      if ($command instanceof Command) {
        $commandMap->register($this->getName(), $command);
      } else {
        throw new PluginException("Tried to register an invalid command.");
      }
    }
  }

  /**
  * Register multiple event listeners.
  * @param Listener[] $listeners An array of Listener instances to register.
  * @return void
  */
  public function registerListeners(array $listeners): void {
    foreach ($listeners as $listener) {
      if ($listener instanceof Listener) {
        $this->server->getPluginManager()->registerEvents($listener, $this);
      } else {
        throw new PluginException("Tried to register an invalid listener.");
      }
    }
  }

  /**
  * Get an DDatabase
  * return mysqli;
  */
  public function getDatabase(): mixed {
    return $this->database;
  }

  /**
  * Get an instance of DatabaseManager.
  */
  public function getDatabaseManager(): DatabaseManager {
    return new DatabaseManager($this);
  }

  /**
  * Gets the data path of the server.
  * @param array|null $join Continue paths.
  * @return string The serve data path.
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
    return trim($path . DIRECTORY_SEPARATOR);
  }

  /**
  * Gets the plugin resources path.
  * @return string The plugin resource path.
  */
  public function getResourcesDirectory(): string {
    return $this->file . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR;
  }

  /**
  * Load all files in /resources plugin.
  * @param string|null $loadType
  * @return array|null
  */
  public function saveRecursiveResources(?string $loadType = '--merge'): ?array {
    if (!is_dir($dir = $this->getResourcesDirectory())) {
      return null;
    }

    $loadedFiles = [];
    try {
      $files = Path::getRecursiveFiles($dir);
      foreach ($files as $file) {
        $processedFile = $this->processFile($file, $loadType);
        if ($processedFile !== null) {
          $loadedFiles[] = $processedFile;
        }
      }
    } catch (Exception $e) {
      throw new PluginException("Erro ao carregar recursos: " . $e->getMessage());
      return null;
    }

    return $loadedFiles;
  }

  /**
  * Process a single file entry from the recursive file listing.
  * @param array $file
  * @param string|null $loadType
  * @return File|null
  */
  private function processFile(array $file, ?string $loadType): ?File {
    $fileName = $file['fileName'] ?? null;
    $fileType = $file['fileType'] ?? null;
    $fileContent = $file['content'] ?? null;
    $fileDirectory = $file['directory'] ?? null;

    if ($fileName === null || $fileType === null || $fileContent === null || $fileDirectory === null) {
      return null;
    }

    $fileExtension = str_replace('file:', '', $fileType);
    $baseFileName = pathinfo($fileName, PATHINFO_FILENAME);
    $relativeDirectory = str_replace([$this->file . '/resources/', '//'], [$this->dataFolder, '/'], $fileDirectory . '/');

    return new File(
      directoryOrConfig: $relativeDirectory,
      fileName: $baseFileName,
      fileType: $fileType,
      autoGenerate: true,
      readCommand: [$loadType => $fileContent]
    );
  }

}