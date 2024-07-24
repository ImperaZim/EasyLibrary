<?php

declare(strict_types = 1);

namespace library\plugin\traits;

use library\filesystem\File;
use library\plugin\PluginToolkit;

/**
* Trait PluginLanguageTrait
* @package library\plugin\traits
*/
trait PluginLanguageTrait {

  /** @var File[] */
  protected array $files = [];

  /** @var string */
  protected string $baseDirectory = 'languages';

  /** @var string */
  protected string $languageDirectory = '';

  /**
  * Sets the base directory for language files.
  * @param string $directory
  */
  public function setLanguageDirectory(string $directory): void {
    $this->baseDirectory = $directory;
  }

  /**
  * Adds language files to the registry from a specified directory.
  * If no files are provided, adds all files in the specified directory.
  * @param string $directory
  * @param array|null $files
  * @param string|null $path
  */
  public function addLanguages(string $directory, ?array $files = null, ?string $path = null): void {
    $basePath = ($path !== null ? $path : $this->baseDirectory) . '/' . $directory;

    if ($files === null || empty($files)) {
      $fileNames = array_diff(scandir($basePath), ['.', '..']);
      $files = array_map(function($file) {
        return pathinfo($file, PATHINFO_FILENAME);
      }, $fileNames);
    } else {
      $files = array_map(function($file) {
        return pathinfo($file, PATHINFO_FILENAME);
      }, $files);
    }

    foreach ($files as $file) {
      $filePath = $basePath . '/' . $file . '.ini';
      if (file_exists($filePath)) {
        $fileInstance = new File(
          directoryOrConfig: $basePath,
          fileName: $file,
          fileType: File::TYPE_INI,
          autoGenerate: true
        );
        if ($fileInstance->fileExists()) {
          $this->files[$file] = $fileInstance;
        }
      }
    }
  }

  /**
  * Retrieves a specific language file or the first registered one if it does not exist.
  * @param string|null $file
  * @return File|null
  */
  public function getLanguage(?string $file = null, ?string $nested = null): ?File {
    if ($file !== null && isset($this->files[$file])) {
      $result = $this->files[$file];
      if ($result !== null && $nested !== null) {
        $result = $result->get($nested);
      }
      return $result;
    }
    return reset($this->files) ?: null;
  }

  /**
  * Retrieves all language file.
  * @return array|null
  */
  public function getLanguages(): ?array {
    return $this->files;
  }

}