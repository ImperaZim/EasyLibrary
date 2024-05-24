<?php

declare(strict_types=1);

namespace internal\invmenu\session\network\handler;

use Closure;
use internal\invmenu\session\network\NetworkStackLatencyEntry;

interface PlayerNetworkHandler{

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry;
}