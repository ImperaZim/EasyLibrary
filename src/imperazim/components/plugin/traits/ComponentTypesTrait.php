<?php

declare(strict_types = 1);

namespace imperazim\components\plugin\traits;

/**
* Trait ComponentTypesTrait
* @package imperazim\components\plugin\traits
*/
trait ComponentTypesTrait {
  public const NETWORK_COMPONENT = 'network';
  public const COMMAND_COMPONENT = 'command';
  public const TRIGGER_COMPONENT = 'trigger';
  public const LISTENER_COMPONENT = 'listener';
  public const SCHEDULER_COMPONENT = 'scheduler';
}