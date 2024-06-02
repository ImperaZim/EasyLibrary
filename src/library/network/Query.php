<?php

declare(strict_types = 1);

namespace library\network;

use function json_decode;
use function json_encode;
use library\server\ServerInfo;
use pocketmine\utils\Internet;
use pocketmine\promise\Promise;
use pocketmine\promise\PromiseResolver;
use pocketmine\utils\InternetRequestResult;

/**
* Class Query
* @package library\network
*/
final class Query {

  /**
  * Gets the minecraft server info.
  * @param string $ip
  * @param int|null $port
  * @return ServerInfo|null
  */
  public static function getServerInfo(string $ip, ?int $port = 19132): ?ServerInfo {
    $url = "https://imperazim.cloud/plugins/EasyLibrary/query/?ip={$ip}&port={$port}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $info = curl_exec($ch);
    if (curl_errno($ch)) {
      return null;
    }
    curl_close($ch);
    return new ServerInfo(json_decode($info, true));
  }

}