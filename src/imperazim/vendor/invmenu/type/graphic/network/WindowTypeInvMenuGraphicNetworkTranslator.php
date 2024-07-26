<?php

declare(strict_types=1);

namespace imperazim\vendor\invmenu\type\graphic\network;

use imperazim\vendor\invmenu\session\InvMenuInfo;
use imperazim\vendor\invmenu\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

final class WindowTypeInvMenuGraphicNetworkTranslator implements InvMenuGraphicNetworkTranslator{

	public function __construct(
		readonly private int $window_type
	){}

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void{
		$packet->windowType = $this->window_type;
	}
}