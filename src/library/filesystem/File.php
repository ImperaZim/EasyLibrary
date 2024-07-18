<?php

declare(strict_types = 1);

namespace library\filesystem;

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
  public static function parseList(string $content): array {
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
  public static function writeList(array $entries): string {
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
        'ini' => self::serializeIniContent($data),
        'txt' => self::writeList(array_keys($data)),
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
        'ini' => self::parseIniContent($fileContent),
        'txt' => array_fill_keys(self::parseList($fileContent), true),
      };
    }
    throw new FileSystemException("Unsupported file type: {$extension}");
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
  * @return mixed The value found at the key path or the default value.
  */
  public function get(?string $keyPath = null, mixed $defaultValue = null): mixed {
    $content = $this->readFile();
    $data = self::deserializeContent($this->fileType, $content);
    if ($keyPath === null) {
      return $data;
    }
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
  * Set a value at a nested key path within the deserialized content and save it back to the file.
  * @param array $keyValuePairs The key-value pairs to set in the deserialized content.
  * @return void
  * @throws FileSystemException If the file cannot be written.
  */
  public function set(array $keyValuePairs): void {
    $content = $this->readFile();
    $data = self::deserializeContent($this->fileType, $content);
    foreach ($keyValuePairs as $keyPath => $value) {
      $keys = explode('.', $keyPath);
      $temp = &$data;
      foreach ($keys as $key) {
        if (!isset($temp[$key])) {
          $temp[$key] = [];
        }
        $temp = &$temp[$key];
      }
      $temp = $value;
    }
    $this->writeFile(self::serializeContent($this->fileType, $data));
  }

  /**
  * Returns a string representation of the File.
  * @return string Returns the string representation.
  */
  public function __toString(): string {
    try {
      return sprintf(
        'File: %s: %s',
        $this->fileName . $this->getFileExtension(),
        $this->readFile()
      );
    } catch (FileSystemException $e) {
      new \crashdump($e);
      return '';
    }
  }

  /**
  * Parse the content of the .ini file.
  * @param string $content The content of the file.
  * @return array
  */
  private static function parseIniContent(string $content): array {
    return parse_ini_string($content, true, INI_SCANNER_TYPED);
  }

  /**
  * Serialize data into .ini format.
  * @param array $data The data to serialize.
  * @return string
  */
  private static function serializeIniContent(array $data): string {
    $result = '';
    foreach ($data as $section => $values) {
      if (is_array($values)) {
        $result .= "[$section]\n";
        foreach ($values as $key => $value) {
          if (is_array($value)) {
            foreach ($value as $v) {
              $result .= "{$key}[] = \"$v\"\n";
            }
          } else {
            $result .= "$key = \"$value\"\n";
          }
        }
      } else {
        $result .= "$section = \"$values\"\n";
      }
    }
    return $result;
  }

  /**
  * Read the .ini file content and return it as an array.
  * @return array
  * @throws FileSystemException If the file cannot be read.
  */
  public function readIniFile(): array {
    $content = $this->readFile();
    return self::parseIniContent($content);
  }

  /**
  * Write data to the .ini file.
  * @param array $data The data to write.
  * @return void
  * @throws FileSystemException If the file cannot be written.
  */
  public function writeIniFile(array $data): void {
    $content = self::serializeIniContent($data);
    $this->writeFile($content);
  }

  /**
  * Get a value from the .ini file.
  * @param string $section The section in the .ini file.
  * @param string $key The key in the section.
  * @param mixed $default The default value to return if the key is not found.
  * @return mixed
  */
  public function getIniValue(?string $section, ?string $key, mixed $default = null): mixed {
    $data = $this->readIniFile();
    if ($section === null && $key === null) {
      return $data ?? $default;
    }
    if ($key === null) {
      return $data[$section] ?? $default;
    }
    return $data[$section][$key] ?? $default;
  }

  /**
  * Set a value in the .ini file.
  * @param string $section The section in the .ini file.
  * @param string $key The key in the section.
  * @param mixed $value The value to set.
  * @return void
  * @throws FileSystemException If the file cannot be written.
  */
  public function setIniValue(string $section, string $key, mixed $value): void {
    $data = $this->readIniFile();
    if (!isset($data[$section])) {
      $data[$section] = [];
    }
    $data[$section][$key] = $value;
    $this->writeIniFile($data);
  }

  /**
  * Delete a value in the .ini file.
  * @param string $section The section in the .ini file.
  * @param string $key The key in the section.
  * @return void
  * @throws FileSystemException If the file cannot be written.
  */
  public function deleteIniValue(string $section, string $key): void {
    $data = $this->readIniFile();
    if (isset($data[$section][$key])) {
      unset($data[$section][$key]);
      $this->writeIniFile($data);
    }
  }
}