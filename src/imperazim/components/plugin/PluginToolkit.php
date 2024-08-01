<?php

declare(strict_types = 1);

namespace imperazim\components\plugin;

use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginLoader;
use pocketmine\plugin\ResourceProvider;
use pocketmine\plugin\PluginDescription;

use Library;
use ReflectionClass;
use imperazim\components\filesystem\Path;
use imperazim\components\filesystem\File;
use imperazim\components\database\DatabaseManager;
use imperazim\components\plugin\exception\PluginException;
use imperazim\components\plugin\traits\ComponentTypesTrait;

/**
* Class PluginToolkit
* @package imperazim\components\plugin
*/
abstract class PluginToolkit extends PluginBase {
  use ComponentTypesTrait;

  /** @var string */
  public ?string $data = null;
  /** @var array */
  private ?array $database = null;

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
    private PluginLoader $loader,
    private Server $server,
    private PluginDescription $description,
    private string $dataFolder,
    private string $file,
    private ResourceProvider $resourceProvider
  ) {
    $this->data = $this->getServerPath(['join:data']);
    
    parent::__construct($loader, $server, $description, $dataFolder, $file, $resourceProvider);
  }

  /**
  * Sets the server motd.
  * @param string $motd.
  * @return self.
  * @throws PluginException.
  */
  public function setMotd(string $motd): self {
    try {
      $motdSplit = explode(':', $motd);
      if (strtolower($motdSplit[0]) === 'language') {
        $motd = str_replace('language:', '', $motd);
        if (method_exists($this, 'getLanguage')) {
          if (($language = $this->getLanguage()) !== null) {
            $motd = $language->get($motd, $motd);
            $this->server->getNetwork()->setName($motd);
          }
        }
        return $this;
      }
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
          return DatabaseManager::connect($database['type'], $database);
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
        $requiredKeys = 'type:host:username:password:database';
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
  * Initializes and registers a component based on its type.
  * @param string $type The type of component ('listener' or 'command').
  * @param mixed $components The component instance to initialize.
  * @return void
  */
  public function initComponents(string $type, mixed $components): void {
    switch (strtolower($type)) {
      case self::NETWORK_COMPONENT:
        if (isset($components['server_name'])) {
          $this->setMotd($components['server_name']);
        }
        break;
      case self::LISTENER_COMPONENT:
        if (!is_array($components)) {
          $components = [$components];
        }
        $listeners = [];
        foreach ($components as $component) {
          if ($component instanceof Listener) {
            $listeners[] = $component;
          }
        }
        $this->registerListeners($listeners);
        break;
      case self::COMMAND_COMPONENT:
        if (!is_array($components)) {
          $components = [$components];
        }
        $commands = [];
        foreach ($components as $component) {
          if ($component instanceof Command) {
            $commands[] = $component;
          }
        }
        $this->registerCommands($commands);
        break;
      default:
        throw new PluginException("Invalid component type: $type. Expected 'listeners' or 'commands'.");
      }
    }

    /**
    * Overwrite commands passed in the argument
    * @param anull<string>|null $commands
    */
    public function overwriteCommands(?array $commands = null): void {
      $this->unregisterCommands(array_keys($commands));
      $this->registerCommands(array_values($commands));
    }

    /**
    * Register a command or multiple commands.
    * @param Command|array $commands Command instance or array of Command instances to register.
    * @return void
    * @throws PluginException If any command is invalid.
    */
    public function registerCommands(Command|array $commands): void {
      try {
        $commandMap = $this->getServer()->getCommandMap();
        $commands = is_array($commands) ? $commands : [$commands];
        foreach ($commands as $command) {
          if ($command instanceof Command) {
            $commandMap->register($this->description->getName(), $command);
          } else {
            throw new PluginException("Tried to register an invalid command.");
          }
        }
      } catch (\Exception $e) {
        new \crashdump($e);
      }
    }

    /**
    * Unregister commands passed in the argument
    * @param anull<string>|null $commands
    */
    public function unregisterCommands(?array $commands = null): void {
      $commandMap = $this->getServer()->getCommandMap();
      foreach ($commands as $commandName) {
        $command = $commandMap->getCommand($commandName);
        if ($command !== null) {
          $commandMap->unregister($command);
        }
      }
    }

    /**
    * Register a listener or multiple listeners.
    * @param Listener|array $listeners Listener instance or array of Listener instances to register.
    * @return void
    * @throws PluginException If any listener is invalid.
    */
    public function registerListeners(Listener|array $listeners): void {
      try {
        $listeners = is_array($listeners) ? $listeners : [$listeners];
        foreach ($listeners as $listener) {
          if ($listener instanceof Listener) {
            $this->server->getPluginManager()->registerEvents($listener, $this);
          } else {
            throw new PluginException("Tried to register an invalid listener.");
          }
        }
      } catch (\Exception $e) {
        new \crashdump($e);
      }
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
            $path .= 'plugin_data' . DIRECTORY_SEPARATOR . $this->description->getName() . DIRECTORY_SEPARATOR;
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
    * Gets the plugin resources path.
    * @return string The plugin resource path.
    * @throws PluginException If there's an error getting the resources directory.
    */
    public function getResourcesDirectory(): string {
      try {
        return $this->file . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR;
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
      } catch (PluginException $e) {
        new \crashdump($e);
      }

      return $loadedFiles;
    }

    /**
    * Process a single file entry from the recursive file listing.
    * @param array $file
    * @param string|null $loadType
    * @return mixed|null
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
        $relativeDirectory = str_replace($this->file . '/resources', $this->dataFolder, $fileDirectory);

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