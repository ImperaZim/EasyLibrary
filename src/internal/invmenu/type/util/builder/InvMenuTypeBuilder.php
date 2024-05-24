<?php

declare(strict_types=1);

namespace internal\invmenu\type\util\builder;

use internal\invmenu\type\InvMenuType;

interface InvMenuTypeBuilder{

	public function build() : InvMenuType;
}