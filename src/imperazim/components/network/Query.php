<?php

declare(strict_types = 1);

namespace imperazim\components\network;

use imperazim\components\network\server\ServerInfo;
use imperazim\components\network\exception\NetworkException;

/**
* Class Query
* @package imperazim\components\network
*/
final class Query {

  /**
  * Gets the Minecraft server info.
  * @param string $ip
  * @param int|null $port
  * @return ServerInfo|null
  */
  public static function getServerInfo(string $ip, ?int $port = 19132): ?ServerInfo {
    try {
      $url = "https://imperazim.cloud/plugins/EasyLibrary/query/?ip={$ip}&port={$port}";

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

      $info = curl_exec($ch);
      if (curl_errno($ch)) {
        throw new NetworkException(curl_error($ch));
      }
      curl_close($ch);
      
      return new ServerInfo(json_decode($info, true));
    } catch (NetworkException $e) {
      new \crashdump($e);
      return null;
    }
  }
}
