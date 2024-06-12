<?php

declare(strict_types = 1);

namespace library\filesystem;

use function rtrim;
use function dirname;
use function implode;
use function explode;
use function basename;
use function array_map;
use function yaml_emit;
use function array_keys;
use function yaml_parse;
use function json_encode;
use function json_decode;
use function str_replace;
use function array_fill_keys;

use const YAML_UTF8_ENCODING;
use const JSON_THROW_ON_ERROR;

use library\utils\Config;
use library\filesystem\trait\FileExtensionTypes;
use library\filesystem\exception\FileSystemException;

/**
* Class File
* @package library\filesystem
*/
final class File {
  use FileExtensionTypes;

  /**
  * File constructor.
  * @param string|Config $directoryOrConfig The directory of the file ou Config.
  * @param string $fileName The name of the file.
  * @param string $fileType The type of the file.
  * @param bool|null $autoGenerate Whether to generate the file if it does not exist.
  * @throws FileSystemException If the file type is invalid or the file cannot be created.
  */
  public function __construct(
    private string|Config $directoryOrConfig,
    private ?string $fileName = null,
    private ?string $fileType = null,
    private ?bool $autoGenerate = false
  ) {
    if ($directoryOrConfig instanceof Config) {
      $config = $directoryOrConfig;
      $filePath = $config->getPath();
      $directory = dirname($filePath);
      $fileName = basename($filePath, str_replace('file:', '.', TYPE_YML));
      $fileType = self::TYPE_YML;
    } else {
      $directory = rtrim($directoryOrConfig, DIRECTORY_SEPARATOR);
    }
    if (!in_array($fileType, $this->getFileTypes())) {
      throw new FileSystemException("Invalid file type: $fileType");
    }
    $this->directoryOrConfig = $directory;
    $this->fileName = $fileName ?? '';
    $this->fileType = $fileType ?? self::TYPE_YML;
    if ($autoGenerate) {
      new Path($directory, true);
      if (!$this->fileExists()) {
        $this->createFile();
      }
    }
  }

  /**
  * Get the full path of the file.
  * @return string
  */
  public function getFilePath(): string {
    return $this->directoryOrConfig . DIRECTORY_SEPARATOR . $this->fileName . $this->getFileExtension();
  }

  /**
  * Get the file type.
  * @return string
  */
  public function getFileType(): string {
    return $this->fileType;
  }

  /**
  * Get the available file extension based on FileExtensionTypes
  * @return array
  */
  private function getFileTypes(): array {
    return [
      self::TYPE_YML,
      self::TYPE_YAML,
      self::TYPE_JSON,
      self::TYPE_TXT
    ];
  }

  /**
  * @return string[]
  */
  public function parseList(string $content) : array {
    $result = [];
    foreach (explode("\n", trim(str_replace("\r\n", "\n", $content))) as $v) {
      $v = trim($v);
      if ($v === "") {
        continue;
      }
      $result[] = $v;
    }
    return $result;
  }

  /**
  * @param string[] $entries
  * @return string
  */
  public function writeList(array $entries) : string {
    return implode("\n", $entries);
  }

  /**
  * Get the file extension based on FileExtensionTypes
  * @param string|null $defaultValue.
  * @return string
  */
  private function getFileExtension(?string $defaultValue = ''): string {
    $extensions = [];
    foreach ($this->getFileTypes() as $ext) {
      $extensions[$ext] = str_replace('file:', '.', $ext);
    }
    return $extensions[$this->fileType] ?? $defaultValue;
  }


  /**
  * Serializes the content based on the file type.
  * @param string $fileType The type of the file (e.g., 'json', 'yml').
  * @param array $data The key-value pairs to be serialized.
  * @return string The serialized content.
  * @throws FileSystemException If the file type is unsupported.
  */
  private function serializeContent(string $fileType, array $data): string {
    $content = match ($fileType) {
      'yml' => yaml_emit($data, YAML_UTF8_ENCODING),
      'yaml' => yaml_emit($data, YAML_UTF8_ENCODING),
      'json' => json_encode($data, JSON_THROW_ON_ERROR),
      'txt' => $this->writeList(array_keys($data)),
      default => throw new FileSystemException("Unsupported file type: {$fileType}")
      };
      return $content;
    }

    /**
    * Deserializes the content based on the file extension.
    * @param string $extension The extension of the file (e.g., 'json', 'yml').
    * @param string $fileContent The content of the file to be deserialized.
    * @return array The deserialized data.
    * @throws FileSystemException If the file type is unsupported.
    */
    /**
    * Deserializes the content based on the file extension.
    * @param string $extension The extension of the file (e.g., 'json', 'yml').
    * @param string $fileContent The content of the file to be deserialized.
    * @return array The deserialized data.
    * @throws FileSystemException If the file type is unsupported.
    */
    private function deserializeContent(string $extension, string $fileContent): array {
      $data = match ($extension) {
        'yml' => yaml_parse($fileContent) ?: [],
        'yaml' => yaml_parse($fileContent) ?: [],
        'json' => json_decode($fileContent, true, 512, JSON_THROW_ON_ERROR),
        'txt' => array_fill_keys($this->parseList($fileContent), true),
      default => throw new FileSystemException("Unsupported file type: $extension")
      };
      return $data;
    }

    /**
    * Load a configuration fload
    * @return boolean
    */
    public function loadConfig(): bool {
      $config = $this->directoryOrConfig;
      if (!$config instanceof Config) {
        return false;
      }
      return $config->getPlugin()->saveResource($config->getPath());
    }

    /**
    * Check if the file exists.
    * @return bool
    */
    public function fileExists(): bool {
      return file_exists($this->getFilePath());
    }

    /**
    * Create the file if it does not exist.
    * @return void
    * @throws FileSystemException If the file cannot be created.
    */
    private function createFile(): void {
      $filePath = $this->getFilePath();
      if (file_put_contents($filePath, '') === false) {
        throw new FileSystemException("Unable to create file: $filePath");
      }
    }

    /**
    * Delete the file.
    * @return void
    * @throws FileSystemException If the file cannot be deleted.
    */
    public function deleteFile(): void {
      $filePath = $this->getFilePath();
      if (!unlink($filePath)) {
        throw new FileSystemException("Unable to delete file: $filePath");
      }
    }

    /**
    * Read the file content.
    * @return string
    * @throws FileSystemException If the file cannot be read.
    */
    public function readFile(): string {
      if (!$this->fileExists()) {
        throw new FileSystemException("File not found: " . $this->getFilePath());
      }
      $content = file_get_contents($this->getFilePath());
      if ($content === false) {
        throw new FileSystemException("Unable to read file: " . $this->getFilePath());
      }
      return $content;
    }

    /**
    * Write content to the file.
    * @param string $content The content to write.
    * @return void
    * @throws FileSystemException If the file cannot be written.
    */
    public function writeFile(string $content): void {
      $result = file_put_contents($this->getFilePath(), $content);
      if ($result === false) {
        throw new FileSystemException("Unable to write to file: " . $this->getFilePath());
      }
    }

    /**
    * Get a value from the file based on the given key path.
    * @param string|null $keyPath The key path to the value.
    * @param mixed $defaultValue The default value to return if the key path is not found.
    * @return mixed The value found at the key path, or the default value if not found.
    */
    public function get(?string $keyPath = null, mixed $defaultValue = null): mixed {
      if ($keyPath === null) {
        $fileContent = $this->readFile();
        $extension = pathinfo($this->getFilePath(), PATHINFO_EXTENSION);
        return $this->deserializeContent($extension, $fileContent);
      }
      $fileContent = $this->readFile();
      $extension = pathinfo($this->getFilePath(), PATHINFO_EXTENSION);
      $data = $this->deserializeContent($extension, $fileContent);
      $keys = explode('.', $keyPath);
      foreach ($keys as $key) {
        if (!isset($data[$key])) {
          return $defaultValue;
        }
        $data = $data[$key];
      }
      return $data;
    }

    /**
    * Set a value in the file based on the given key path.
    * @param array $keyValuePairs The key-value pairs to set.
    * @return bool true se a operação for bem-sucedida, false se falhar.
    */
    public function set(array $keyValuePairs): bool {
      $fileContent = $this->readFile();
      $extension = pathinfo($this->getFilePath(), PATHINFO_EXTENSION);
      if (isset($keyValuePairs['-all']) && is_array($keyValuePairs['-all'])) {
        $content = $this->serializeContent($extension, $keyValuePairs['-all']);
        try {
          $this->writeFile($content);
          return true;
        } catch (FileSystemException $e) {
          return false;
        }
      }
      $data = $this->deserializeContent($extension, $fileContent);
      foreach ($keyValuePairs as $keyPath => $value) {
        $keys = explode('.', $keyPath);
        $nestedArray = &$data;
        foreach ($keys as $key) {
          if (!is_array($nestedArray)) {
            $nestedArray = [];
          }
          if (!isset($nestedArray[$key])) {
            $nestedArray[$key] = [];
          }
          $nestedArray = &$nestedArray[$key];
        }
        $nestedArray = $value;
      }
      $content = $this->serializeContent($extension, $data);
      try {
        $this->writeFile($content);
        return true;
      } catch (FileSystemException $e) {
        return false;
      }
    }

    /**
    * Returns a string representation of the File.
    * @return string Returns the string representation.
    */
    public function __toString(): string {
      $type = $this->getFileExtension('Unknown');
      return sprintf(
        'File (Type: %s) %s: %s',
        strtoupper($type),
        $this->fileName . '.' . pathinfo($this->fileName, PATHINFO_EXTENSION),
        $this->readFile()
      );
    }

  }