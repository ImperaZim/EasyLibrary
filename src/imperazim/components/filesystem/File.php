<?php

declare(strict_types = 1);

namespace imperazim\components\filesystem;

use imperazim\components\config\Config;
use imperazim\components\filesystem\traits\FileExtensionTypes;
use imperazim\components\filesystem\exception\FileSystemException;

/**
* Class File
* @package imperazim\components\filesystem
*/
final class File {
  use FileExtensionTypes;

  /**
  * File constructor.
  * @param string|Config|null $directoryOrConfig The directory of the file or Config instance.
  * @param string|null $fileName The name of the file.
  * @param string|null $fileType The type of the file.
  * @param bool|null $autoGenerate Whether to generate the file if it does not exist.
  * @param array|null $readCommand The initial query.
  * @param bool|null $clone Get this class with empty data.
  * @throws FileSystemException If the file type is invalid or the file cannot be created.
  */
  public function __construct(
    private string|Config|null $directoryOrConfig = null,
    private ?string $fileName = null,
    private ?string $fileType = null,
    private ?bool $autoGenerate = false,
    private ?array $readCommand = null,
    private ?bool $clone = false
  ) {
    if (!$clone) {
      if ($directoryOrConfig instanceof Config) {
        $config = $directoryOrConfig;
        $filePath = $config->getPath();
        $directory = dirname($filePath);
        $fileName = basename($filePath, str_replace('file:', '.', self::TYPE_YML));
        $fileType = self::TYPE_YML;
      } else {
        $directory = str_replace('//', '/', $directoryOrConfig . '/');
      }
      if (!in_array($fileType, self::getTypes())) {
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
        if ($readCommand !== null) {
          $this->set($readCommand);
        }
      }
    }
  }

  /**
  * Clone the File object with empty data.
  * @return self
  */
  public static function clone(): self {
    return new self(clone: true);
  }

  /**
  * Get the full path of the file.
  * @return string
  */
  public function getFilePath(): string {
    return $this->directoryOrConfig . $this->fileName . $this->getFileExtension();
  }

  /**
  * Get the file type.
  * @return string
  */
  public function getFileType(): string {
    return $this->fileType;
  }

  /**
  * Parse a list from a string content.
  * @param string $content The content to parse.
  * @return string[]
  */
  public static function parseList(string $content) : array {
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
  * Write a list to a string.
  * @param string[] $entries The list of entries.
  * @return string
  */
  public static function writeList(array $entries) : string {
    return implode("\n", $entries);
  }

  /**
  * Get the file extension based on FileExtensionTypes.
  * @param string|null $defaultValue The default value if the extension is not found.
  * @return string
  */
  private function getFileExtension(?string $defaultValue = ''): string {
    return self::getExtensionByType($this->fileType, true) ?? $defaultValue;
  }

  /**
  * Serialize content based on the file type.
  * @param string $extension The type of the file (e.g., 'json', 'yml').
  * @param array $data The key-value pairs to be serialized.
  * @return string The serialized content.
  * @throws FileSystemException If the file type is unsupported.
  */
  public static function serializeContent(string $extension, array $data): string {
    if (in_array($extension, self::getExtensions())) {
      return match ($extension) {
        'yml' => yaml_emit($data, YAML_UTF8_ENCODING),
        'yaml' => yaml_emit($data, YAML_UTF8_ENCODING),
        'json' => json_encode($data, JSON_PRETTY_PRINT),
        'txt' => self::writeList(array_keys($data)),
        'ini' => self::writeIniFile($data)
      };
    }
    throw new FileSystemException("Unsupported file type: {$extension}");
  }

  /**
  * Deserialize content based on the file extension.
  * @param string $extension The extension of the file (e.g., 'json', 'yml').
  * @param string $fileContent The content of the file to be deserialized.
  * @return array The deserialized data.
  * @throws FileSystemException If the file type is unsupported.
  */
  public static function deserializeContent(string $extension, string $fileContent): array {
    if (in_array($extension, self::getExtensions())) {
      return match ($extension) {
        'yml' => yaml_parse($fileContent) ?: [],
        'yaml' => yaml_parse($fileContent) ?: [],
        'json' => json_decode(empty($fileContent) ? "{}" : $fileContent, true, 512, JSON_PRETTY_PRINT),
        'txt' => array_fill_keys(self::parseList($fileContent), true),
        'ini' => parse_ini_string($fileContent, true)
      };
    }
    throw new FileSystemException("Unsupported file type: {$extension}");
  }

  /**
  * Write content to an INI file format.
  * @param array $data The data to be written.
  * @return string The INI file content as a string.
  */
  private static function writeIniFile(array $data): string {
    $content = '';
    foreach ($data as $section => $values) {
      if (is_array($values)) {
        $content .= "[$section]\n";
        foreach ($values as $key => $value) {
          if (is_array($value)) {
            foreach ($value as $arrayValue) {
              $content .= "{$key}[] = " . (is_numeric($arrayValue) ? $arrayValue : "\"$arrayValue\"") . "\n";
            }
          } else {
            $content .= "$key = " . (is_numeric($value) ? $value : "\"$value\"") . "\n";
          }
        }
      } else {
        $content .= "$section = " . (is_numeric($values) ? $values : "\"$values\"") . "\n";
      }
    }
    return $content;
  }


  /**
  * Load a configuration file.
  * @return bool
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
    try {
      $filePath = $this->getFilePath();
      if (file_put_contents($filePath, '') === false) {
        throw new FileSystemException("Unable to create file: $filePath");
      }
    } catch (FileSystemException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Delete the file.
  * @return void
  * @throws FileSystemException If the file cannot be deleted.
  */
  public function deleteFile(): void {
    try {
      $filePath = $this->getFilePath();
      if (!unlink($filePath)) {
        throw new FileSystemException("Unable to delete file: $filePath");
      }
    } catch (FileSystemException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Read the file content.
  * @return string
  * @throws FileSystemException If the file cannot be read.
  */
  public function readFile(): string {
    try {
      if (!$this->fileExists()) {
        throw new FileSystemException("File not found: " . $this->getFilePath());
      }
      $content = file_get_contents($this->getFilePath());
      if ($content === false) {
        throw new FileSystemException("Unable to read file: " . $this->getFilePath());
      }
      return $content;
    } catch (FileSystemException $e) {
      new \crashdump($e);
      return '';
    }
  }

  /**
  * Write content to the file.
  * @param string $content The content to write.
  * @return void
  * @throws FileSystemException If the file cannot be written.
  */
  public function writeFile(string $content): void {
    try {
      $result = file_put_contents($this->getFilePath(), $content);
      if ($result === false) {
        throw new FileSystemException("Unable to write to file: " . $this->getFilePath());
      }
    } catch (FileSystemException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Retrieve the content of the file or a nested value within the deserialized content.
  * @param ?string $keyPath The dot-separated key path to access a nested value. If null, the whole deserialized content is returned.
  * @param mixed $defaultValue The default value to return if the key path is not found.
  * @return mixed The content or nested value.
  */
  public function get(?string $keyPath = null, mixed $defaultValue = null): mixed {
    try {
      $fileContent = $this->readFile();
      $extension = self::getExtensionByType($this->getFileType());
      $data = self::deserializeContent($extension, $fileContent);

      if ($keyPath === null) {
        return $data;
      }

      $keys = explode('.', $keyPath);
      foreach ($keys as $key) {
        if (!is_array($data) || !array_key_exists($key, $data)) {
          return $defaultValue;
        }
        $data = $data[$key];
      }
      return $data;
    } catch (FileSystemException $e) {
      new \crashdump($e);
      return $defaultValue;
    }
  }

  /**
  * Set a value in the file based on the given key path.
  * @param array $keyValuePairs The key-value pairs to set.
  * @return bool True if the operation is successful, false if it fails.
  */
  public function set(array $keyValuePairs): bool {
    try {
      $fileContent = $this->readFile();
      $extension = self::getExtensionByType($this->getFileType());
      $data = self::deserializeContent($extension, $fileContent);

      if (isset($keyValuePairs['--override']) && is_array($keyValuePairs['--override'])) {
        $content = self::serializeContent($extension, $keyValuePairs['--override']);
        try {
          self::writeFile($content);
          return true;
        } catch (FileSystemException $e) {
          return false;
        }
      }

      if (isset($keyValuePairs['--merge']) && is_array($keyValuePairs['--merge'])) {
        $this->mergeArrays($data, $keyValuePairs['--merge']);
      } else {
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
      }

      $content = self::serializeContent($extension, $data);
      try {
        self::writeFile($content);
        return true;
      } catch (FileSystemException $e) {
        return false;
      }
    } catch (FileSystemException $e) {
      new \crashdump($e);
      return false;
    }
  }

  /**
  * Merge two arrays.
  * @param array $original The original array.
  * @param array $newData The new data to merge.
  * @return void
  */
  private function mergeArrays(array &$original, array $newData): void {
    foreach ($newData as $key => $value) {
      if (is_array($value) && isset($original[$key]) && is_array($original[$key])) {
        $this->mergeArrays($original[$key], $value);
      } else {
        if (!isset($original[$key])) {
          $original[$key] = $value;
        }
      }
    }
  }

  /**
  * Returns a string representation of the File.
  * @return string Returns the string representation.
  */
  public function __toString(): string {
    try {
      $type = $this->getFileExtension('Unknown');
      return sprintf(
        'File (Type: %s) %s: %s',
        strtoupper($type),
        $this->fileName . '.' . pathinfo($this->fileName, PATHINFO_EXTENSION),
        $this->readFile()
      );
    } catch (FileSystemException $e) {
      new \crashdump($e);
      return '';
    }
  }
}