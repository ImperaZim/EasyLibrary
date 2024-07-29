<?php

declare(strict_types = 1);

namespace imperazim\ui;

use imperazim\ui\command\form\FormCommand;
use imperazim\ui\command\menu\MenuCommand;
use imperazim\ui\command\dialogue\DialogueCommand;

use imperazim\components\filesystem\File;
use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\PluginComponent;
use imperazim\components\plugin\traits\PluginComponentsTrait;

/**
* Class UiManager
* @package imperazim\ui
*/
final class UiManager extends PluginComponent {
  use PluginComponentsTrait;

  /**
  * Initializes the hud component.
  * @param PluginToolkit $plugin The Plugin.
  */
  public static function init(PluginToolkit $plugin): array {
    /**
    * Define the main instance to make the code easier and there is no need to define getInstance in main if you are adding it just for that! However, it is not mandatory.
    */
    self::setPlugin(plugin: $plugin);

    /**
    * In collaboration with the File class, the getFile method makes it easier to define a way to obtain data from a file, simply using self::getFile(token);
    */
    self::setFile(token: 'forms', file: new File(
      directoryOrConfig: $plugin->data, // Directory
      fileName: "forms", // File Name
      fileType: File::TYPE_YML, // File Type (view on FileTypes)
      autoGenerate: true, // Creates the file if it does not exist.
      readCommand: ["--merge" => [
        "long_form" => [],
        "modal_form" => [],
        "custom_form" => [],
      ]] // Defines a value in the file, the --merge key is used in case you add a value to the array passed in the command and it does not exist in the file, it is added automatically.
    ));
    self::setFile(token: 'menus', file: new File(
      directoryOrConfig: $plugin->data, // Directory
      fileName: "menus", // File Name
      fileType: File::TYPE_YML, // File Type (view on FileTypes)
      autoGenerate: true, // Creates the file if it does not exist.
      readCommand: ["--merge" => [
        "chest_menu" => [],
        "double_chest_menu" => [],
        "hopper_menu" => [],
      ]] // Defines a value in the file, the --merge key is used in case you add a value to the array passed in the command and it does not exist in the file, it is added automatically.
    ));
    self::setFile(token: 'dialogues', file: new File(
      directoryOrConfig: $plugin->data, // Directory
      fileName: "dialogues", // File Name
      fileType: File::TYPE_YML, // File Type (view on FileTypes)
      autoGenerate: true, // Creates the file if it does not exist.
      readCommand: ["--merge" => [
        "simple_dialogue" => []
      ]] // Defines a value in the file, the --merge key is used in case you add a value to the array passed in the command and it does not exist in the file, it is added automatically.
    ));

    /**
    * Registers the subcomponents of the current component.
    * View on ComponentTypes [COMMAND, LISTENER, SCHEDULER, NETWORK]
    */
    return [
      self::COMMAND_COMPONENT => [
        new FormCommand(),
        new MenuCommand(),
        new DialogueCommand(),
      ]
    ];
  }

}