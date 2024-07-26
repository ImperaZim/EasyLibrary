<?php

declare(strict_types = 1);

namespace imperazim\components\network\server;

/**
* Class ServerInfo
* @package imperazim\components\network\server
*/
final class ServerInfo {

  /**
  * ServerInfo constructor.
  * @param array $info
  */
  public function __construct(private array $info) {
    /** TODO null */
  }

  /**
  * Gets the online status of the server.
  * @return bool
  */
  public function getOnline(): bool {
    return $this->info['online'] ?? false;
  }

  /**
  * Gets the game type.
  * @return string
  */
  public function getGameType(): string {
    return $this->info['gametype'] ?? 'SMP';
  }

  /**
  * Gets the game ID.
  * @return string
  */
  public function getGameId(): string {
    return $this->info['game_id'] ?? 'MINECRAFTPE';
  }

  /**
  * Gets the IP address of the server.
  * @return string
  */
  public function getIp(): string {
    return $this->info['ip'] ?? '0.0.0.0';
  }

  /**
  * Gets the port of the server.
  * @return string
  */
  public function getPort(): string {
    return $this->info['port'] ?? '19132';
  }

  /**
  * Gets the hostname of the server.
  * @return string
  */
  public function getHostname(): string {
    return $this->info['hostname'] ?? 'PocketMine-MP Server';
  }

  /**
  * Gets the version of the server.
  * @return string
  */
  public function getVersion(): string {
    return $this->info['version'] ?? 'vXX.X.X';
  }

  /**
  * Gets the server engine.
  * @return string
  */
  public function getServerEngine(): string {
    return $this->info['server_engine'] ?? 'PocketMine-MP X.XX.X';
  }

  /**
  * Gets the list of players on the server.
  * @return PlayerList
  */
  public function getPlayers(): PlayerList {
    return new PlayerList($this->info['players'] ?? []);
  }

  /**
  * Gets the default world of the server.
  * @return string
  */
  public function getDefaultWorld(): string {
    return $this->info['map'] ?? 'world';
  }

  /**
  * Gets the whitelist status of the server.
  * @return bool
  */
  public function getWhitelist(): bool {
    return $this->info['whitelist'] ?? true;
  }

  /**
  * Gets the list of plugins on the server.
  * @return array
  */
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

  /**
  * Gets the number of online players.
  * @return int
  */
  public function getOnline(): int {
    return $this->players['online'] ?? 0;
  }

  /**
  * Gets the maximum number of slots.
  * @return int
  */
  public function getMaxSlot(): int {
    return $this->players['max'] ?? 20;
  }

  /**
  * Gets the list of players.
  * @return array
  */
  public function getList(): array {
    return $this->players['list'] ?? [];
  }

}