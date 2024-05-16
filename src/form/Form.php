<?php

namespace form;

use form\forms\BlankForm;
use pocketmine\player\Player;
use pocketmine\utils\CloningRegistryTrait;

/**
* Class Form
* @package form
*/
final class Form {
  use CloningRegistryTrait;

  /**
  * Set up and register forms.
  */
  public static function setup() : void {
    self::register('example_menu_form', fn(Player $player, ?array $data = []) : FormInterface => new forms\ExampleMenuForm($player, $data));
    self::register('example_modal_form', fn(Player $player, ?array $data = []) : FormInterface => new forms\ExampleModalForm($player, $data));
    self::register('example_custom_form', fn(Player $player, ?array $data = []) : FormInterface => new forms\ExampleCustomForm($player, $data));
    // ADD YOIR FORMS WHERE
    // self::register('form_identifier', fn(Player $player, ?array $data = []) : FormInterface => new YourFormClass($player, $data));
    // call with: Form::get($player, 'form_identifier', ['data with necessary']);
  }

  /**
  * Get a form instance.
  * @param Player $player
  * @param string $form
  * @param array|null $data
  * @return FormInterface
  */
  public static function get(Player $player, string $form, ?array $data = []): FormInterface {
    try {
      return Form::getAll()[strtoupper($form)]($player, $data);
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
    return new BlankForm($player, $data);
  }

  /**
  * Get all registered forms.
  * @return array
  */
  public static function getAll() : array {
    return self::_registryGetAll();
  }

  /**
  * Register a form.
  * @param string $name
  * @param \Closure $form
  */
  protected static function register(string $name, \Closure $form) : void {
    self::_registryRegister($name, $form);
  }
}