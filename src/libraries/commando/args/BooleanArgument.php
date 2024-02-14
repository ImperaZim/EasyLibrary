<?php

declare(strict_types=1);

namespace libraries\commando\args;

use pocketmine\command\CommandSender;

class BooleanArgument extends StringEnumArgument {

	protected const VALUES = [
		"true" => true,
		"false" => false,
	];

	public function getTypeName(): string {
		return "bool";
	}

	public function getEnumName(): string {
		return "bool";
	}

	public function parse(string $argument, CommandSender $sender) {
		return $this->getValue($argument);
	}
}