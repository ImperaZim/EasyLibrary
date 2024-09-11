<?php

declare(strict_types = 1);

namespace imperazim\components\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use imperazim\vendor\commando\BaseCommand;
use imperazim\components\command\CommandManager;
use imperazim\vendor\commando\args\RawStringArgument;

/**
* Class GeneratePluginCommand
* @package imperazim\components\command\defaults
*/
final class GeneratePluginCommand extends BaseCommand {

  /**
  * GeneratePluginCommand constructor.
  */
  public function __construct() {
    parent::__construct(
      plugin: CommandManager::getPlugin(),
      names: ['genplugin'],
      description: 'Generate a new plugin base with EasyLibrary.'
    );
  }

  /**
  * Prepares the command for execution.
  */
  protected function prepare(): void {
    $this->setPermission('easylibrary.command.genplugin');
    $this->registerArguments([
      0 => new RawStringArgument('PluginName', true),
      1 => new RawStringArgument('PluginVersion', true),
      2 => new RawStringArgument('PluginApiVersion', true),
      3 => new RawStringArgument('PluginAuthor', true),
      4 => new RawStringArgument('AuthorInDir_Y/N', true)
    ]);
  }

  /**
  * Executes the command.
  * @param mixed $sender
  * @param string $aliasUsed
  * @param array $args
  */
  public function onRun(mixed $sender, string $aliasUsed, array $args): void {
    $main = CommandManager::getPlugin();
    $pluginName = $args['PluginName'] ?? "Plugin";
    $pluginVersion = $args['PluginVersion'] ?? "1.0.0";
    $pluginApiVersion = $args['PluginApiVersion'] ?? "5.0.0";
    $pluginAuthor = $args['PluginAuthor'] ?? "Unknown";
    $authorInDir = strtolower($args['AuthorInDir_Y/N'] ?? "n") === "y" ? "y" : "n";
    $useEasyLibrary = "y";

    $currentDir = $main->getServer()->getDataPath() . "plugins";
    $dirPath = $currentDir . "/" . $pluginName;

    if (is_dir($dirPath)) {
      $sender->sendMessage(TextFormat::RED . "Error: The directory '$pluginName' already exists.");
      return;
    }

    mkdir($dirPath, 0777, true);

    $mainNamespace = ($authorInDir === "y") ? "$pluginAuthor\\$pluginName" : $pluginName;

    $pluginYmlContent = "name: $pluginName\nversion: $pluginVersion\napi: $pluginApiVersion\nmain: $mainNamespace\n";
    if ($pluginAuthor !== "Unknown") {
      $pluginYmlContent .= "author: $pluginAuthor\n";
    }
    $pluginYmlContent .= "depend:\n  - EasyLibrary\n";

    file_put_contents($dirPath . "/plugin.yml", $pluginYmlContent);

    // Criar diretório de código fonte
    $srcDir = ($authorInDir === "y") ? "$dirPath/src/$pluginAuthor" : "$dirPath/src";
    mkdir($srcDir, 0777, true);

    // Gerar classe principal do plugin
    $classContent = "<?php\n\ndeclare(strict_types=1);\n\n";
    if ($authorInDir === "y") {
      $classContent .= "namespace $pluginAuthor;\n\n";
    }

    $classContent .= "use imperazim\\components\\plugin\\PluginToolkit;\nuse imperazim\\components\\plugin\\traits\\PluginToolkitTrait;\n\n";
    $classContent .= "class $pluginName extends PluginToolkit {\n";
    $classContent .= "    use PluginToolkitTrait;\n\n";
    $classContent .= "    protected function onEnable(): void {\n";
    $classContent .= "        \$this->saveRecursiveResources();\n";
    $classContent .= "        \$this->getLogger()->info(\"$pluginName with EasyLibrary enabled!\");\n";
    $classContent .= "    }\n";
    $classContent .= "}\n";

    file_put_contents("$srcDir/$pluginName.php", $classContent);

    $sender->sendMessage(TextFormat::GREEN . "Plugin $pluginName successfully created!");

    return;
  }
}