<?php

declare(strict_types = 1);

namespace imperazim\vendor\simplepackethandler;

use InvalidArgumentException;
use pocketmine\plugin\Plugin;
use pocketmine\event\EventPriority;

use imperazim\vendor\simplepackethandler\monitor\PacketMonitor;
use imperazim\vendor\simplepackethandler\monitor\IPacketMonitor;
use imperazim\vendor\simplepackethandler\interceptor\PacketInterceptor;
use imperazim\vendor\simplepackethandler\interceptor\IPacketInterceptor;

final class SimplePacketHandler {

  public static function createInterceptor(Plugin $registerer, int $priority = EventPriority::NORMAL, bool $handle_cancelled = false) : IPacketInterceptor {
    if ($priority === EventPriority::MONITOR) {
      throw new InvalidArgumentException("Cannot intercept packets at MONITOR priority");
    }
    return new PacketInterceptor($registerer, $priority, $handle_cancelled);
  }

  public static function createMonitor(Plugin $registerer, bool $handle_cancelled = false) : IPacketMonitor {
    return new PacketMonitor($registerer, $handle_cancelled);
  }
}