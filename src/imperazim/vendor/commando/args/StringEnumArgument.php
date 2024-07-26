<?php

declare(strict_types = 1);

namespace imperazim\vendor\commando\args;

use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

use function implode;
use function array_map;
use function array_keys;
use function preg_match;
use function strtolower;

abstract class StringEnumArgument extends BaseArgument {

  protected const VALUES = [];

  public function __construct(string $name, bool $optional = false) {
    parent::__construct($name, $optional);

    $this->parameterData->enum = new CommandEnum($this->getEnumName(), $this->getEnumValues());
  }

  public function getNetworkType(): int {
    // this will be disregarded by PM anyways because this will be considered as a string enum
    return -1;
  }

  public function canParse(string $testString, CommandSender $sender): bool {
    return (bool)preg_match(
      "/^(" . implode("|", array_map("\\strtolower", $this->getEnumValues())) . ")$/iu",
      $testString
    );
  }

  public abstract function getEnumName(): string;

  public function getValue(string $string) {
    return static::VALUES[strtolower($string)];
  }

  public function getEnumValues(): array {
    return array_keys(static::VALUES);
  }
}