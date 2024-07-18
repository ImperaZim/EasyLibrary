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

  /** @var File|null */
  protected ?File $file = null;

  /** @return File|null */
  public function getLanguage(): ?File {
    return $this->file;
  }

  /**
  * @param PluginToolkit $plugin
  * @param string|null $file
  */
  public function setLanguage(PluginToolkit $plugin, ?string $file = 'language'): void {
    $fileInstance = new File(
      directoryOrConfig: $plugin->getServerPath(['join:data']),
      fileName: $file,
      fileType: File::TYPE_INI,
      autoGenerate: true
    );
    if ($fileInstance->fileExists()) {
      $this->file = $fileInstance;
    }
  }

}