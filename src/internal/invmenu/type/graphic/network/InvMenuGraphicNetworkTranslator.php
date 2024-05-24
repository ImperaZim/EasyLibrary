<?php

declare(strict_types=1);

namespace internal\invmenu\type\graphic\network;

use internal\invmenu\session\InvMenuInfo;
use internal\invmenu\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

interface InvMenuGraphicNetworkTranslator{

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void;
}