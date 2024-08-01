<?php

declare(strict_types=1);

namespace imperazim\vendor\invmenu\type\graphic\network;

use imperazim\vendor\invmenu\session\InvMenuInfo;
use imperazim\vendor\invmenu\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

interface InvMenuGraphicNetworkTranslator{

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void;
}