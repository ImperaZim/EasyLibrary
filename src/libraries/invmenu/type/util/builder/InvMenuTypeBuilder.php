<?php

declare(strict_types=1);

namespace libraries\invmenu\type\util\builder;

use libraries\invmenu\type\InvMenuType;

interface InvMenuTypeBuilder{

	public function build() : InvMenuType;
}