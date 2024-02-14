<?php

declare(strict_types=1);

namespace libraries\invmenu\session;

use libraries\invmenu\InvMenu;
use libraries\invmenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		readonly public InvMenu $menu,
		readonly public InvMenuGraphic $graphic,
		readonly public ?string $graphic_name
	){}
}