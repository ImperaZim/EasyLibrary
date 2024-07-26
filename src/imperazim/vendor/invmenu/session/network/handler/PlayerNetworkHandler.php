<?php

declare(strict_types=1);

namespace imperazim\vendor\invmenu\session\network\handler;

use Closure;
use imperazim\vendor\invmenu\session\network\NetworkStackLatencyEntry;

interface PlayerNetworkHandler{

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry;
}