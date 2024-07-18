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

use ReflectionClass;
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
  private ?array $database = null;

  private PluginLoader $loader;
  private Server $server;
  private PluginDescription $description;
  private string $dataFolder;
  private string $file;
  private ResourceProvider $resourceProvider;

  /**
  * PluginToolkit construct
  * @param PluginLoader $loader The plugin loader.
  * @param Server $server The server instance.
  * @param PluginDescription $description The plugin description.
  * @param string $dataFolder The path to the data folder.
  * @param string $file The file path.
  * @param ResourceProvider $resourceProvider The resource provider.
  */
  public function __construct(
    PluginLoader $loader,
    Server $server,
    PluginDescription $description,
    string $dataFolder,
    string $file,
    ResourceProvider $resourceProvider
  ) {
    $this->loader = $loader;
    $this->server = $server;
    $this->description = $description; 
    $this->dataFolder = $dataFolder;
    $this->file = $file; 
    $this->resourceProvider = $resourceProvider;
    
    parent::__construct($loader, $server, $description, $dataFolder, $file, $resourceProvider);
  }

  /**
  * Sets the server motd.
  * @param string $motd.
  * @return self.
  * @throws PluginException If the database configuration is invalid.
  */
  public function setMotd(string $motd): self {
    try {
      $this->server->getNetwork()->setName($motd);
    } catch (PluginException $e) {
      new \crashdump($e);
    }
    return $this;
  }

  /**
  * Retrieves the database configuration.
  * @return mixed The database instance.
  * @throws PluginException If the database configuration is invalid.
  */
  public function getDatabase(): mixed {
    try {
      $childClass = get_class($this);
      if (property_exists($childClass, 'database')) {
        $database = (new ReflectionClass($childClass))->getProperty('database')->getValue($this);
        if ($this->validateDatabaseConfig($database)) {
          return DatabaseManager::connect(...array_values($database));
        } else {
          throw new PluginException("Database configuration is invalid.");
        }
      }
    } catch (PluginException $e) {
      new \crashdump($e);
    }
    return $this->database;
  }

  /**
  * Validates the database configuration array.
  * @param array|null $database The database configuration array to validate.
  * @return bool True if the configuration is valid, false otherwise.
  * @throws PluginException If validation fails.
  */
  private function validateDatabaseConfig(?array $database): bool {
    try {
      if (is_array($database)) {
        $requiredKeys = 'host:username:password:database';
        foreach (explode(':', $requiredKeys) as $key) {
          if (!array_key_exists($key, $database)) {
            return false;
          }
        }
        return true;
      }
      return false;
    } catch (PluginException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Register multiple commands.
  * @param Command[] $commands An array of Command instances to register.
  * @return void
  * @throws PluginException If any command is invalid.
  */
  public function registerCommands(array $commands): void {
    try {
      $commandMap = $this->getServer()->getCommandMap();
      foreach ($commands as $command) {
        if ($command instanceof Command) {
          $commandMap->register($this->getName(), $command);
        } else {
          throw new PluginException("Tried to register an invalid command.");
        }
      }
    } catch (PluginException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Register multiple event listeners.
  * @param Listener[] $listeners An array of Listener instances to register.
  * @return void
  * @throws PluginException If any listener is invalid.
  */
  public function registerListeners(array $listeners): void {
    try {
      foreach ($listeners as $listener) {
        if ($listener instanceof Listener) {
          $this->server->getPluginManager()->registerEvents($listener, $this);
        } else {
          throw new PluginException("Tried to register an invalid listener.");
        }
      }
    } catch (PluginException $e) {}
  }

  /**
  * Gets the data path of the server.
  * @param array|null $join Continue paths.
  * @return string The server data path.
  * @throws PluginException If there's an error generating the path.
  */
  public function getServerPath(?array $join = null): string {
    try {
      $path = $this->server->getDataPath();
      if ($join !== null) {
        if (strtolower($join[0]) === 'join:data') {
          $path .= 'plugin_data' . DIRECTORY_SEPARATOR . $this->getName() . DIRECTORY_SEPARATOR;
        } else {
          $path .= implode(DIRECTORY_SEPARATOR, $join) . DIRECTORY_SEPARATOR;
        }
      }
      return $path;
    } catch (PluginException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Load all files in /resources plugin.
  * @param string|null $loadType
  * @return array|null
  * @throws PluginException If there's an error loading resources.
  */
  public function saveRecursiveResources(?string $loadType = '--merge'): ?array {
    if (!is_dir($dir = $this->file . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR)) {
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
    } catch (PluginException $e) {
      new \crashdump($e);
    }

    return $loadedFiles;
  }

  /**
  * Process a single file entry from the recursive file listing.
  * @param array $file
  * @param string|null $loadType
  * @return File|null
  * @throws PluginException If there's an error processing the file.
  */
  private function processFile(array $file, ?string $loadType): ?File {
    try {
      $fileName = $file['fileName'] ?? null;
      $fileType = $file['fileType'] ?? null;
      $fileContent = $file['content'] ?? null;
      $fileDirectory = $file['directory'] ?? null;

      if ($fileName === null || $fileType === null || $fileContent === null || $fileDirectory === null) {
        return null;
      }

      $baseFileName = pathinfo($fileName, PATHINFO_FILENAME);
      $relativeDirectory = str_replace("plugins/{$this->getName()}/resources", "plugin_data/{$this->getName()}", $fileDirectory);

      return new File(
        directoryOrConfig: $relativeDirectory,
        fileName: $baseFileName,
        fileType: $fileType,
        autoGenerate: true,
        readCommand: [$loadType => $fileContent]
      );
    } catch (PluginException $e) {
      new \crashdump($e);
    }
  }
}