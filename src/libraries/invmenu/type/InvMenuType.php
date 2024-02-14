<?php

declare(strict_types=1);

namespace libraries\invmenu\type;

use libraries\invmenu\InvMenu;
use libraries\invmenu\type\graphic\InvMenuGraphic;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;

interface InvMenuType{

	public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic;

	public function createInventory() : Inventory;
}