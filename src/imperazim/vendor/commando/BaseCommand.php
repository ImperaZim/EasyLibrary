<?php

declare(strict_types = 1);

namespace imperazim\vendor\commando;

use imperazim\vendor\commando\traits\IArgumentable;
use imperazim\vendor\commando\traits\ArgumentableTrait;
use imperazim\vendor\commando\constraint\BaseConstraint;
use imperazim\vendor\commando\exception\InvalidErrorCode;

use pocketmine\plugin\Plugin;
use InvalidArgumentException;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginOwned;
use pocketmine\command\CommandSender;

use function count;
use function dechex;
use function str_replace;
use function array_shift;
use function array_unique;
use function array_unshift;

abstract class BaseCommand extends Command implements IArgumentable, IRunnable, PluginOwned {
  use ArgumentableTrait;

  public const ERR_INVALID_ARG_VALUE = 0x01;
  public const ERR_TOO_MANY_ARGUMENTS = 0x02;
  public const ERR_INSUFFICIENT_ARGUMENTS = 0x03;
  public const ERR_NO_ARGUMENTS = 0x04;
  public const ERR_INVALID_ARGUMENTS = 0x05;

  /** @var string[] */
  protected $errorMessages = [
    self::ERR_INVALID_ARG_VALUE => TextFormat::RED . "Invalid value '{value}' for argument #{position}. Expecting: {expected}.",
    self::ERR_TOO_MANY_ARGUMENTS => TextFormat::RED . "Too many arguments given.",
    self::ERR_INSUFFICIENT_ARGUMENTS => TextFormat::RED . "Insufficient number of arguments given.",
    self::ERR_NO_ARGUMENTS => TextFormat::RED . "No arguments are required for this command.",
    self::ERR_INVALID_ARGUMENTS => TextFormat::RED . "Invalid arguments supplied.",
  ];

  /** @var CommandSender */
  protected CommandSender $currentSender;

  /** @var BaseSubCommand[] */
  private array $subCommands = [];

  /** @var BaseConstraint[] */
  private array $constraints = [];

  /** @var Plugin */
  protected Plugin $plugin;

  public function __construct(
    Plugin $plugin,
    array $names,
    Translatable|string $description = ""
) {
    $name = array_shift($names);
    $aliases = $names;
    $this->plugin = $plugin;
    parent::__construct($name, $description, null, $aliases);
    $this->prepare();
    $this->usageMessage = $this->generateUsageMessage();
}


  public function getOwningPlugin(): Plugin {
    return $this->plugin;
  }

  final public function execute(mixed $sender, string $commandLabel, array $args) {
    $this->currentSender = $sender;
    if (!$this->testPermission($sender)) {
      return;
    }
    /** @var BaseCommand|BaseSubCommand $cmd */
    $cmd = $this;
    $passArgs = [];
    if (count($args) > 0) {
      if (isset($this->subCommands[($label = $args[0])])) {
        array_shift($args);
        $this->subCommands[$label]->execute($sender, $label, $args);
        return;
      }

      $passArgs = $this->attemptArgumentParsing($cmd, $args);
    } elseif ($this->hasRequiredArguments()) {
      $this->sendError(self::ERR_INSUFFICIENT_ARGUMENTS);
      return;
    }
    if ($passArgs !== null) {
      foreach ($cmd->getConstraints() as $constraint) {
        if (!$constraint->test($sender, $commandLabel, $passArgs)) {
          $constraint->onFailure($sender, $commandLabel, $passArgs);
          return;
        }
      }
      $cmd->onRun($sender, $commandLabel, $passArgs);
    }
  }

  public function sendConsoleError() : void {
    $this->getOwningPlugin()->getLogger()->error('Comando bloqueado no console!');
  }

  /**
  * @param ArgumentableTrait $ctx
  * @param array             $args
  *
  * @return array|null
  */
  private function attemptArgumentParsing($ctx, array $args): ?array {
    $dat = $ctx->parseArguments($args, $this->currentSender);
    if (!empty(($errors = $dat["errors"]))) {
      foreach ($errors as $error) {
        $this->sendError($error["code"], $error["data"]);
      }

      return null;
    }

    return $dat["arguments"];
  }

  abstract public function onRun(mixed $player, string $aliasUsed, array $args): void;

  protected function sendUsage(): void {
    $this->currentSender->sendMessage(TextFormat::RED . "Usage: " . $this->getUsage());
  }

  public function sendError(int $errorCode, array $args = []): void {
    $str = (string)$this->errorMessages[$errorCode];
    foreach ($args as $item => $value) {
      $str = str_replace('{' . $item . '}', (string) $value, $str);
    }
    $this->currentSender->sendMessage($str);
    $this->sendUsage();
  }

  public function setErrorFormat(int $errorCode, string $format): void {
    if (!isset($this->errorMessages[$errorCode])) {
      throw new InvalidErrorCode("Invalid error code 0x" . dechex($errorCode));
    }
    $this->errorMessages[$errorCode] = $format;
  }

  public function setErrorFormats(array $errorFormats): void {
    foreach ($errorFormats as $errorCode => $format) {
      $this->setErrorFormat($errorCode, $format);
    }
  }
  
  /**
  * Register multiple subcommands at once.
  * @param array $subcommands An array of BaseSubCommand instances. If no position is specified, the index will be used.
  * @throws ArgumentOrderException
  */
  public function registerSubcommands(array $subcommands): void {
    foreach ($subcommands as $subCommand) {
      $this->registerSubCommand($subCommand);
    }
  }

  public function registerSubCommand(BaseSubCommand $subCommand): void {
    $keys = $subCommand->getAliases();
    array_unshift($keys, $subCommand->getName());
    $keys = array_unique($keys);
    foreach ($keys as $key) {
      if (!isset($this->subCommands[$key])) {
        $subCommand->setParent($this);
        $this->subCommands[$key] = $subCommand;
      } else {
        throw new InvalidArgumentException("SubCommand with same name / alias for '$key' already exists");
      }
    }
  }

  /**
  * @return BaseSubCommand[]
  */
  public function getSubCommands(): array {
    return $this->subCommands;
  }

  public function addConstraint(BaseConstraint $constraint) : void {
    $this->constraints[] = $constraint;
  }

  /**
  * @return BaseConstraint[]
  */
  public function getConstraints(): array {
    return $this->constraints;
  }

  public function getUsageMessage(): string {
    return $this->getUsage();
  }

  public function setCurrentSender(CommandSender $sender): void {
    $this->currentSender = $sender;
  }
}