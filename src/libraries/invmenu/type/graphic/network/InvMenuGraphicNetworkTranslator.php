<?php

declare(strict_types=1);

namespace libraries\invmenu\type\graphic\network;

use libraries\invmenu\session\InvMenuInfo;
use libraries\invmenu\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

interface InvMenuGraphicNetworkTranslator{

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void;
}