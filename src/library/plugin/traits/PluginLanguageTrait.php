<?php

declare(strict_types = 1);

namespace library\plugin\traits;

use library\filesystem\File;

/**
* Trait PluginLanguageTrait
* @package library\plugin\traits
*/
trait PluginLanguageTrait {

  /** @var File|null */
  protected ?File $file = null;

  /** @return File */
  public function getLanguage(): File {
    return $this->file;
  }

  /** @param File $file */
  public function setLanguage(File $file): void {
    $this->file = $file;
  }

}