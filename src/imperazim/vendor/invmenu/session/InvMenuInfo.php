<?php

declare(strict_types=1);

namespace imperazim\vendor\invmenu\session;

use imperazim\vendor\invmenu\InvMenu;
use imperazim\vendor\invmenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		readonly public InvMenu $menu,
		readonly public InvMenuGraphic $graphic,
		readonly public ?string $graphic_name
	){}
}