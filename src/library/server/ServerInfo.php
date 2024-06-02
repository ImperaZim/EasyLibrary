<?php

declare(strict_types = 1);

namespace library\server;

/**
* Class ServerInfo
* @package library\server
*/
final class ServerInfo {

  /**
  * ServerInfo constructor.
  * @param array $info
  */
  public function __construct(private array $info) {
    /** TODO null */
  }

  public function getOnline(): bool {
    return $this->info['online'] ?? false;
  }

  public function getGameType(): string {
    return $this->info['gametype'] ?? 'SMP';
  }

  public function getGameId(): string {
    return $this->info['game_id'] ?? 'MINECRAFTPE';
  }

  public function getIp(): string {
    return $this->info['ip'] ?? '0.0.0.0';
  }

  public function getPort(): string {
    return $this->info['port'] ?? '19132';
  }

  public function getHostname(): string {
    return $this->info['hostname'] ?? 'PocketMine-MP Server';
  }

  public function getVersion(): string {
    return $this->info['version'] ?? 'vXX.X.X';
  }

  public function getServerEngine(): string {
    return $this->info['server_engine'] ?? 'PocketMine-MP X.XX.X';
  }

  public function getPlayers(): PlayerList {
    return new PlayerList($this->info['players'] ?? []);
  }

  public function getDefaultWorld(): string {
    return $this->info['map'] ?? 'world';
  }

  public function getWhitelist(): bool {
    return $this->info['whitelist'] ?? true;
  }

  public function getPlugins(): array {
    return $this->info['plugins'] ?? [];
  }

}

/**
* Class PlayerList
* @package library\server
*/
final class PlayerList {

  /**
  * PlayerList constructor.
  * @param array $players
  */
  public function __construct(private array $players) {
    /** TODO null */
  }

  public function getOnline(): int {
    $this->players['online'] ?? 0;
  }

  public function getMaxSlot(): int {
    $this->players['max'] ?? 20;
  }

  public function getList(): array {
    $this->players['list'] ?? [];
  }

}