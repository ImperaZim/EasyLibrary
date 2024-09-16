<?php

declare(strict_types = 1);

namespace imperazim\components\command\subcommand;

/**
* Class SubcommandBuilder
* @package imperazim\components\command\subcommand
*/
final class SubcommandBuilder {

  /**
  * SubcommandBuilder constructor.
  *
  * @param array $commandData The array containing command configuration.
  */
  public function __construct(
    private array $names = [],
    private string $description = '',
    private ?string $permission = null,
    private array $arguments = [],
    private array $constraints = []
  ) {}

  /**
  * Get command names.
  *
  * @return array
  */
  public function getNames(): array {
    return $this->names;
  }

  /**
  * Get command description.
  *
  * @return string
  */
  public function getDescription(): string {
    return $this->description;
  }

  /**
  * Get command permission.
  *
  * @return string|null
  */
  public function getPermission(): ?string {
    return $this->permission;
  }

  /**
  * Get command arguments.
  *
  * @return array
  */
  public function getArguments(): array {
    return $this->arguments;
  }

  /**
  * Get command constraints.
  *
  * @return array
  */
  public function getConstraints(): array {
    return $this->constraints;
  }
}