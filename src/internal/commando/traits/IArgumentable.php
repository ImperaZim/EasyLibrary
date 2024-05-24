<?php

declare(strict_types = 1);

namespace internal\commando\traits;

use pocketmine\command\CommandSender;
use internal\commando\args\BaseArgument;

interface IArgumentable {
  public function generateUsageMessage(string $parent = ""): string;
  public function hasArguments(): bool;

  /**
  * @return BaseArgument[][]
  */
  public function getArgumentList(): array;
  public function parseArguments(array $rawArgs, CommandSender $sender): array;
  public function registerArgument(int $position, BaseArgument $argument): void;
}