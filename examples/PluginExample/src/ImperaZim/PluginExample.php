<?php

declare(strict_types = 1);

namespace ImperaZim;

use library\network\Query;
use library\filesystem\File;
use library\plugin\PluginToolkit;
use ImperaZim\commands\FormExampleCommand;
use ImperaZim\commands\MenuExampleCommand;
use ImperaZim\commands\DialogueExampleCommand;

use pocketmine\utils\SingletonTrait;

/**
* Class PluginExample
* @package ImperaZim
*/
final class PluginExample extends PluginToolkit {
  use SingletonTrait;

  /** @var File */
  private File $settings;

  /**
  * Called when the plugin is loaded.
  */
  protected function onLoad(): void {
    self::setInstance($this);
  }

  /**
  * Called when the plugin is enabled.
  */
  protected function onEnable(): void {
    $this->settings = new File(
      directoryOrConfig: $this->getServerPath(join: ['tests']),
      fileName: 'settings',
      fileType: File::TYPE_YML,
      autoGenerate: true
    );
    $this->settings->set([
      '-all' => [
        'form_command_name' => ['form'],
        'form_command_description' => '§7Form example command!',
      ]
    ]);
    $this->getServer()->getCommandMap()->registerAll(
      fallbackPrefix: 'PluginExample',
      commands: [
        FormExampleCommand::base(),
        MenuExampleCommand::base(),
        DialogueExampleCommand::base()
      ]
    );
  }

  /**
  * Get the message using id.
  * @param string $way
  * @param mixed  $default
  * @param array  $tags
  * @return mixed
  */
  public static function getSettings(string $way,
    mixed $default = null,
    array $tags = []): mixed {
    $messages = PluginExample::getInstance()->settings;
    $result = $messages->get($way,
      $default);
    if (is_array($result)) {
      return $result;
    } else {
      if (is_numeric($result)) {
        return $result;
      }
      $tags['{PREFIX}'] = $messages->get('prefix', '§l§8(§c!§8)§r');
      return str_replace(array_keys($tags), array_values($tags), $result);
    }
  }

}