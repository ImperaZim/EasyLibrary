<?php

declare(strict_types = 1);

namespace library\network;

use function json_decode;
use function json_encode;
use pocketmine\utils\Internet;
use pocketmine\promise\Promise;
use pocketmine\promise\PromiseResolver;

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
  public static function getServerInfo(string $ip, ?int $port = 19132): Promise {
    $url = "https://imperazim.cloud/plugins/EasyLibrary/query/";
    $postData = json_encode([
      'ip' => $ip,
      'port' => $port
    ]);

    $resolver = new PromiseResolver();

    Internet::postURL(
      $url, 
      $postData, 
      10, 
      [], 
      function (string $response) use ($resolver): void {
      $data = json_decode($response, true);
      if (json_last_error() !== JSON_ERROR_NONE) {
        $resolver->reject([]);
        return;
      }
      $resolver->resolve($data);
    },
      function () use ($resolver): void {
        $resolver->reject([]);
      }
     );

    return $resolver->getPromise();
  }

}