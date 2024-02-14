<?php

declare(strict_types = 1);

namespace libraries\commando\constraint;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class ConsoleRequiredConstraint extends BaseConstraint {

  public function test(CommandSender $sender, string $aliasUsed, array $args): bool {
    return $this->isVisibleTo($sender);
  }

  public function onFailure(CommandSender $sender, string $aliasUsed, array $args): void {
    $sender->sendMessage(TextFormat::RED . "This command must be executed from a server console."); // f*ck off grammar police
  }

  public function isVisibleTo(CommandSender $sender): bool {
    return !($sender instanceof Player);
  }
}