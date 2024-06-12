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
    $types = [
      File::TYPE_JSON,
      File::TYPE_YAML,
      File::TYPE_YML
    ];
    foreach ($types as $type) {
      $this->settings = new File(
        directoryOrConfig: $this->getServerPath(
          join: ['plugin_data', $this->getName(), 'tests']
        ),
        fileName: 'settings',
        fileType: $type,
        autoGenerate: true
      );
      $this->settings->set([
        '--merge' => [
          'form' => [
            'command' => [
              'name' => ['form'],
              'description' => '§7Form example command!'
            ],
            'data' => [
              'long_form' => [
                'title' => 'LongForm Example Title',
                'content' => '§7Any text:',
                'buttons' => [
                  'button_1' => [
                    'text' => 'Button with url image',
                    'image' => 'url|https://picsum.photos/200/200'
                  ],
                  'button_2' => [
                    'text' => ['Button with resourcepack image', 'Content example'],
                    'image' => 'path|textures/items/diamond_sword.png'
                  ]
                ]
              ],
              'modal_form' => [
                'title' => 'ModalForm Example Title',
                'content' => '§7Any text:',
                'buttons' => [
                  'button_yes' => [
                    'text' => 'Button Yes'
                  ],
                  'button_no' => [
                    'text' => 'Button No'
                  ]
                ]
              ],
              'custom_form' => [
                'title' => 'CustomForm Example Title'
              ]
            ]
          ],
          'menu' => [
            'command' => [
              'name' => ['menu'],
              'description' => '§7Menu example command!'
            ],
            'data' => [
              'title' => 'Menu Example Name',
              'items' => []
            ]
          ],
          'dialogue' => [
            'command' => [
              'name' => ['dialogue'],
              'description' => '§7Dialogue example command!'
            ],
            'data' => [
              'name' => 'Dialogue Example Name',
              'text' => 'Dialogue Example Text',
              'texture' => [
                'type' => 'dialogue:default',
                'typeId' => 0,
              ],
              'buttons' => [
                0 => 'Button 1',
                1 => 'Button 2'
              ]
            ]
          ]
        ]
      ]);
      var_dump($this->settings->get('form.command'));
    }
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