<?php

declare(strict_types = 1);

namespace library\network;

/**
* Class Query
*/
final class Query {
  /** @var string|null */
  private $token = null;
  /** @var resource|false */
  private $socket;
  /** @var string */
  private $errstr = "";

  const SESSION_ID = 2;
  const TYPE_STAT = 0x00;
  const TYPE_HANDSHAKE = 0x09;

  /**
  * Query constructor.
  *
  * @param string|array $host
  * @param int $port
  * @param int $timeout
  * @param bool $auto_connect
  */
  public function __construct(
    private $host,
    private $port = 25565,
    private $timeout = 3,
    $auto_connect = false
  ) {

    if (is_array($host)) {
      $this->host = $host['host'];
      $this->port = empty($host['port'])?$port:$host['port'];
      $this->timeout = empty($host['timeout'])?$timeout:$host['timeout'];
      $auto_connect = empty($host['auto_connect'])?$auto_connect:$host['auto_connect'];
    }

    if ($auto_connect === true) {
      $this->connect();
    }

  }

  /**
  * Get error message.
  *
  * @return string
  */
  public function get_error(): string {
    return $this->errstr;
  }

  /**
  * Check if connected.
  *
  * @return bool
  */
  public function is_connected(): bool {
    if (empty($this->token)) return false;
    return true;
  }

  /**
  * Disconnect from server.
  */
  public function disconnect(): void {
    if ($this->socket) {
      fclose($this->socket);
    }
  }

  /**
  * Connect to server.
  *
  * @return bool
  */
  public function connect(): bool {
    $this->socket = fsockopen('udp://' . $this->host, $this->port, $errno, $errstr, $this->timeout);

    if (!$this->socket) {
      $this->errstr = $errstr;
      return false;
    }

    stream_set_timeout($this->socket, $this->timeout);
    stream_set_blocking($this->socket, true);

    return $this->get_challenge();

  }

  /**
  * Get handshake challenge.
  *
  * @return bool
  */
  private function get_challenge(): bool {
    if (!$this->socket) {
      return false;
    }

    $packet = pack("c3N", 0xFE, 0xFD, Query::TYPE_HANDSHAKE, Query::SESSION_ID);

    if (fwrite($this->socket, $packet, strlen($packet)) === FALSE) {
      $this->errstr = "Unable to write to socket";
      return false;
    }

    $response = fread($this->socket, 2056);

    if (empty($response)) {
      $this->errstr = "Unable to authenticate connection";
      return false;
    }

    $response_data = unpack("c1type/N1id/a*token", $response);

    if (!isset($response_data['token']) || empty($response_data['token'])) {
      $this->errstr = "Unable to authenticate connection.";
      return false;
    }

    $this->token = $response_data['token'];

    return true;

  }

  /**
  * Get server information.
  *
  * @return array|bool
  */
  public function get_info() {
    if (!$this->is_connected()) {
      $this->errstr = "Not connected to host";
      return false;
    }
    $packet = pack("c3N2", 0xFE, 0xFD, Query::TYPE_STAT, Query::SESSION_ID, $this->token);

    $packet = $packet . pack("c4", 0x00, 0x00, 0x00, 0x00);

    if (!fwrite($this->socket, $packet, strlen($packet))) {
      $this->errstr = "Unable to write to socket.";
      return false;
    }

    $response = fread($this->socket, 16);
    $response_data = unpack("c1type/N1id", $response);
    $response = fread($this->socket, 2056);
    $payload = explode ("\x00\x01player_\x00\x00", $response);
    $info_raw = explode("\x00", rtrim($payload[0], "\x00"));
    $info = array();
    foreach (array_chunk($info_raw, 2) as $pair) {
      list($key, $value) = $pair;
      if ($key == "hostname") {
        $value = $this->strip_color_codes($value);
      }
      $info[$key] = $value;
    }

    $players_raw = rtrim($payload[1], "\x00");
    $players = array();
    if (!empty($players_raw)) {
      $players = explode("\x00", $players_raw);
    }

    $info['players'] = $players;

    return $info;
  }

  /**
  * Strip color codes from string.
  *
  * @param string $string
  * @return string
  */
  public function strip_color_codes(string $string): string {
    return preg_replace('/[\x00-\x1F\x80-\xFF]./', '', $string);
  }

}