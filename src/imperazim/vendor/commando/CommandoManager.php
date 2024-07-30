<?php

declare(strict_types = 1);

namespace imperazim\vendor\commando;

use ReflectionClass;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\EventPriority;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandOverload;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\PluginComponent;
use imperazim\components\plugin\traits\PluginComponentsTrait;

use imperazim\vendor\commando\store\SoftEnumStore;
use imperazim\vendor\commando\traits\IArgumentable;
use imperazim\vendor\simplepackethandler\SimplePacketHandler;
use imperazim\vendor\commando\exception\HookAlreadyRegistered;

use function count;
use function array_unshift;

/**
* Class CommandoManager
* @package imperazim\vendor\commando
*/
final class CommandoManager extends PluginComponent implements Listener {
  use PluginComponentsTrait;

  /** @var bool */
  private static bool $isIntercepting = false;

  /**
  * Initializes the commando component.
  * @param PluginToolkit $plugin The Plugin.
  */
  public static function init(PluginToolkit $plugin): array {
    $interceptor = SimplePacketHandler::createInterceptor($plugin, EventPriority::HIGHEST, false);
    $interceptor->interceptOutgoing(function(AvailableCommandsPacket $pk, NetworkSession $target) : bool {
      if (!self::$isIntercepting) {
        $p = $target->getPlayer();
        foreach ($pk->commandData as $commandName => $commandData) {
          $cmd = Server::getInstance()->getCommandMap()->getCommand($commandName);
          if ($cmd instanceof BaseCommand) {
            foreach ($cmd->getConstraints() as $constraint) {
              if (!$constraint->isVisibleTo($p)) {
                continue 2;
              }
            }
            $pk->commandData[$commandName]->overloads = self::generateOverloads($p, $cmd);
          }
        }
        $pk->softEnums = SoftEnumStore::getEnums();
        self::$isIntercepting = true;
        $target->sendDataPacket($pk);
        self::$isIntercepting = false;
      }
      return self::$isIntercepting;
    });

    self::setPlugin(plugin: $plugin);
    return [
      self::LISTENER_COMPONENT => [
        new self()
      ]
    ];
  }

  /**
  * @param CommandSender $cs
  * @param BaseCommand $command
  *
  * @return CommandOverload[]
  */
  private static function generateOverloads(CommandSender $cs,
    BaseCommand $command): array {
    $overloads = [];

    foreach ($command->getSubCommands() as $label => $subCommand) {
      if (!$subCommand->testPermissionSilent($cs) || $subCommand->getName() !== $label) {
        // hide aliases
        continue;
      }
      foreach ($subCommand->getConstraints() as $constraint) {
        if (!$constraint->isVisibleTo($cs)) {
          continue 2;
        }
      }
      $scParam = new CommandParameter();
      $scParam->paramName = $label;
      $scParam->paramType = AvailableCommandsPacket::ARG_FLAG_VALID | AvailableCommandsPacket::ARG_FLAG_ENUM;
      $scParam->isOptional = false;
      $scParam->enum = new CommandEnum($label, [$label]);

      $overloadList = self::generateOverloads($cs, $subCommand);
      if (!empty($overloadList)) {
        foreach ($overloadList as $overload) {
          $overloads[] = new CommandOverload(false, [$scParam, ...$overload->getParameters()]);
        }
      } else {
        $overloads[] = new CommandOverload(false, [$scParam]);
      }
    }

    foreach (self::generateOverloadList($command) as $overload) {
      $overloads[] = $overload;
    }

    return $overloads;
  }

  /**
  * @param IArgumentable $argumentable
  *
  * @return CommandOverload[]
  */
  private static function generateOverloadList(IArgumentable $argumentable): array {
    $input = $argumentable->getArgumentList();
    $combinations = [];
    $outputLength = array_product(array_map("count", $input));
    $indexes = [];
    foreach ($input as $k => $charList) {
      $indexes[$k] = 0;
    }
    do {
      /** @var CommandParameter[] $set */
      $set = [];
      foreach ($indexes as $k => $index) {
        $param = $set[$k] = clone $input[$k][$index]->getNetworkParameterData();

        if (isset($param->enum) && $param->enum instanceof CommandEnum) {
          $refClass = new ReflectionClass(CommandEnum::class);
          $refProp = $refClass->getProperty("enumName");
          $refProp->setAccessible(true);
          $refProp->setValue($param->enum, "enum#" . spl_object_id($param->enum));
        }
      }
      $combinations[] = new CommandOverload(false, $set);

      foreach ($indexes as $k => $v) {
        $indexes[$k]++;
        $lim = count($input[$k]);
        if ($indexes[$k] >= $lim) {
          $indexes[$k] = 0;
          continue;
        }
        break;
      }
    } while (count($combinations) !== $outputLength);

    return $combinations;
  }
}