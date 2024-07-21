<?php

declare(strict_types = 1);

namespace library\plugin\traits;

/**
* Trait PluginComponents
* @package library\plugin\traits
*/
trait PluginComponents {

  /**
  * Constant for the component type 'network|command|listener'.
  * @var string
  */
  public const NETWORK_COMPONENT = 'network';
  public const COMMAND_COMPONENT = 'command';
  public const LISTENER_COMPONENT = 'listener';

}