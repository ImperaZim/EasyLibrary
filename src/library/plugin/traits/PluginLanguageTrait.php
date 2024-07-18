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

  /** @var string|null */
  protected ?string $file = null;

  /** @return File */
  public function getLanguage(): File {
    return $this->file;
  }

  /** @param string $file */
  public function setLanguage(PluginToolkit $plugin, ?string $file = 'language'): void {
    $file = new File(
      directoryOrConfig: $plugin->getServerPath(['join:data']),
      fileName: $file,
      fileType: File::TYPE_INI
    );
    if ($file->fileExists()) {
      $this->file = $file;
    }
  }

}