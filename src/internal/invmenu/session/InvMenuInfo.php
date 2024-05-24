<?php

declare(strict_types=1);

namespace internal\invmenu\session;

use internal\invmenu\InvMenu;
use internal\invmenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		readonly public InvMenu $menu,
		readonly public InvMenuGraphic $graphic,
		readonly public ?string $graphic_name
	){}
}