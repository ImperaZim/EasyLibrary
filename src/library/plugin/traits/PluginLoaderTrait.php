<?php

declare(strict_types = 1);

namespace library\plugin\traits;

/**
* Trait PluginLoaderTrait
* @package library\plugin\traits
*/
trait PluginLoaderTrait {
  
  /** @var self|null Holds the singleton instance. */
  private static ?self $instance = null;

  /**
  * Creates a new instance of the class.
  * @return self A new instance of the class.
  */
  private static function createInstance() : self {
    return new self();
  }

  /**
  * Gets the singleton instance of the class.
  * @return self The singleton instance.
  */
  public static function getInstance() : self {
    if (self::$instance === null) {
      self::$instance = self::createInstance();
    }
    return self::$instance;
  }

  /**
  * Sets the singleton instance of the class.
  * @param self $instance The instance to set.
  * @return void
  */
  public static function setInstance(self $instance) : void {
    self::$instance = $instance;
  }

  /**
  * Resets the singleton instance of the class to null.
  * @return void
  */
  public static function resetInstance() : void {
    self::$instance = null;
  }
  
}