<?php

declare(strict_types=1);

namespace imperazim\vendor\invmenu\type;

use imperazim\vendor\invmenu\InvMenu;
use imperazim\vendor\invmenu\type\graphic\InvMenuGraphic;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;

interface InvMenuType{

	public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic;

	public function createInventory() : Inventory;
}