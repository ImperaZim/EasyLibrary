<?php

declare(strict_types=1);

namespace imperazim\vendor\invmenu\type\util\builder;

use imperazim\vendor\invmenu\type\InvMenuType;

interface InvMenuTypeBuilder{

	public function build() : InvMenuType;
}