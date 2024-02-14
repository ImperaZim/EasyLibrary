<?php

declare(strict_types = 1);

namespace libraries\commando;

use function trim;

abstract class BaseSubCommand extends BaseCommand {
  /** @var BaseCommand */
  protected BaseCommand $parent;

  public function __construct(string $name, string $description = "", array $aliases = []) {
    parent::__construct($name, $description, $aliases);

    $this->usageMessage = "";
  }

  public function getParent(): BaseCommand {
    return $this->parent;
  }

  /**
  * @param BaseCommand $parent
  *
  * @internal Used to pass the parent context from the parent command
  */
  public function setParent(BaseCommand $parent): void {
    $this->parent = $parent;
  }

  public function getUsage(): string {
    if (empty($this->usageMessage)) {
      $parent = $this->parent;
      $parentNames = "";

      while ($parent instanceof BaseSubCommand) {
        $parentNames = $parent->getName() . $parentNames;
        $parent = $parent->getParent();
      }

      if ($parent instanceof BaseCommand) {
        $parentNames = $parent->getName() . " " . $parentNames;
      }

      $this->usageMessage = $this->generateUsageMessage(trim($parentNames));
    }

    return $this->usageMessage;
  }
}