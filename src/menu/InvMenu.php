<?php

namespace menu;

use player\Player;
use menu\menus\BlankMenu;
use pocketmine\utils\CloningRegistryTrait;

/**
* Class InvMenu
* @package menu
*/
final class InvMenu {
  use CloningRegistryTrait;

  /**
  * Set up and register menus.
  */
  public static function setup() : void {
    self::register('example_menu', fn(Player $player, ?array $data = []) : InvMenuInterface => new menus\ExampleMenu($player, $data));
    
    // ADD YOUR MENUS WHERE
    // self::register('menu_identifier', fn(Player $player, ?array $data = []) : InvMenuInterface => new YourMenuClass($player, $data));
    // call with: InvMenu::get($player, 'menu_identifier', ['data with necessary']);
  }

  /**
  * Get a menu instance.
  * @param Player $player
  * @param string $menu
  * @param array|null $data
  * @return InvMenuInterface
  */
  public static function get(Player $player, string $menu, ?array $data = []): InvMenuInterface {
    try {
      return InvMenu::getAll()[strtoupper($menu)]($player, $data);
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
    return new Blank($player, $data);
  }

  /**
  * Get all registered menus.
  * @return array
  */
  public static function getAll() : array {
    return self::_registryGetAll();
  }

  /**
  * Register a menu.
  * @param string $name
  * @param \Closure $menu
  */
  protected static function register(string $name, \Closure $menu) : void {
    self::_registryRegister($name, $menu);
  }
}