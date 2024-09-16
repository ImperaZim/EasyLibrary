<?php

declare(strict_types = 1);

namespace imperazim\components\command\defaults;

use pocketmine\lang\KnownTranslationFactory;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\permission\DefaultPermissionNames;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;
use pocketmine\VersionInfo;
use function count;
use function implode;
use function sprintf;
use function stripos;
use function strtolower;
use const PHP_VERSION;

use imperazim\components\command\Command;
use imperazim\components\command\CommandBuilder;
use imperazim\components\plugin\PluginToolkit;


/**
* Class VersionCommand
* @package imperazim\components\command\defaults
*/
final class VersionCommand extends Command {

  /**
  * Builds the command with the given parameters.
  * @param PluginToolkit $plugin The plugin toolkit instance used to register the command.
  * @return CommandBuilder
  */
  public function build(PluginToolkit $plugin): CommandBuilder {
    return new CommandBuilder(
      names: ['version', 'ver', 'about'],
      description: 'See data about the server.',
      permission: DefaultPermissionNames::COMMAND_VERSION
    );
  }

  /**
  * Executes the command.
  * @param PluginToolkit $plugin
  * @param mixed $sender
  * @param string $aliasUsed
  * @param array $args
  */
  public function run(PluginToolkit $plugin, mixed $sender, string $aliasUsed, array $args): void {
    try {
      if (count($args) === 0) {
        $library = 'EasyLibrary';

        $sender->sendMessage("This server is running " . TextFormat::GREEN . VersionInfo::NAME . " §rwith " . TextFormat::GREEN . $library);
        $sender->sendMessage($library . ' version: ' . TextFormat::GREEN . $plugin->getDescription()->getVersion() . ' §r(with ' . TextFormat::GREEN . count($plugin->enabledComponents) . '§r enabled\'s component\'s)');

        $versionColor = VersionInfo::IS_DEVELOPMENT_BUILD ? TextFormat::YELLOW : TextFormat::GREEN;
        $sender->sendMessage(KnownTranslationFactory::pocketmine_command_version_serverSoftwareVersion(
          $versionColor . VersionInfo::VERSION()->getFullVersion() . TextFormat::RESET,
          TextFormat::GREEN . VersionInfo::GIT_HASH() . TextFormat::RESET
        ));

        $sender->sendMessage(KnownTranslationFactory::pocketmine_command_version_minecraftVersion(
          TextFormat::GREEN . ProtocolInfo::MINECRAFT_VERSION_NETWORK . TextFormat::RESET,
          TextFormat::GREEN . ProtocolInfo::CURRENT_PROTOCOL . TextFormat::RESET
        ));

        $sender->sendMessage(KnownTranslationFactory::pocketmine_command_version_phpVersion(TextFormat::GREEN . PHP_VERSION . TextFormat::RESET));

        $jitMode = Utils::getOpcacheJitMode();
        if ($jitMode !== null) {
          if ($jitMode !== 0) {
            $jitStatus = KnownTranslationFactory::pocketmine_command_version_phpJitEnabled(sprintf("CRTO: %d", $jitMode));
          } else {
            $jitStatus = KnownTranslationFactory::pocketmine_command_version_phpJitDisabled();
          }
        } else {
          $jitStatus = KnownTranslationFactory::pocketmine_command_version_phpJitNotSupported();
        }

        $sender->sendMessage(KnownTranslationFactory::pocketmine_command_version_phpJitStatus($jitStatus->format(TextFormat::GREEN, TextFormat::RESET)));

        $sender->sendMessage(KnownTranslationFactory::pocketmine_command_version_operatingSystem(TextFormat::GREEN . Utils::getOS() . TextFormat::RESET));
      } else {
        $pluginName = implode(" ", $args);
        $exactPlugin = $sender->getServer()->getPluginManager()->getPlugin($pluginName);

        if ($exactPlugin instanceof Plugin) {
          $this->describeToSender($exactPlugin, $sender);

          return;
        }

        $found = false;
        $pluginName = strtolower($pluginName);
        foreach ($sender->getServer()->getPluginManager()->getPlugins() as $plugin) {
          if (stripos($plugin->getName(), $pluginName) !== false) {
            $this->describeToSender($plugin, $sender);
            $found = true;
          }
        }

        if (!$found) {
          $sender->sendMessage(KnownTranslationFactory::pocketmine_command_version_noSuchPlugin());
        }
      }
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  private function describeToSender(Plugin $plugin, mixed $sender) : void {
    $desc = $plugin->getDescription();
    $sender->sendMessage(TextFormat::DARK_GREEN . $desc->getName() . TextFormat::RESET . " version " . TextFormat::DARK_GREEN . $desc->getVersion());

    if ($desc->getDescription() !== "") {
      $sender->sendMessage($desc->getDescription());
    }

    if ($desc->getWebsite() !== "") {
      $sender->sendMessage("Website: " . $desc->getWebsite());
    }

    if (count($authors = $desc->getAuthors()) > 0) {
      if (count($authors) === 1) {
        $sender->sendMessage("Author: " . implode(", ", $authors));
      } else {
        $sender->sendMessage("Authors: " . implode(", ", $authors));
      }
    }
  }

}