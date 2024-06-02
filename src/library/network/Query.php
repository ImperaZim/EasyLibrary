<?php

declare(strict_types = 1);

namespace library\network;

use function json_decode;
use function json_encode;
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
  * @return Promise<array>
  */
  public static function getServerInfo(string $ip, ?int $port = 19132): mixed {
    $url = "https://imperazim.cloud/plugins/EasyLibrary/query/";
    $data = array(
      'ip' => $ip,
      'port' => $port
    );
    $options = array(
      'http' => array(
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data)
      )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) {
      return "Erro ao fazer a solicitação HTTP";
    } else {
      return $result;
    }
  }
}