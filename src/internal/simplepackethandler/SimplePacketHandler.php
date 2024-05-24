<?php

declare(strict_types = 1);

namespace internal\simplepackethandler;

use InvalidArgumentException;
use pocketmine\plugin\Plugin;
use pocketmine\event\EventPriority;

use internal\simplepackethandler\monitor\PacketMonitor;
use internal\simplepackethandler\monitor\IPacketMonitor;
use internal\simplepackethandler\interceptor\PacketInterceptor;
use internal\simplepackethandler\interceptor\IPacketInterceptor;

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