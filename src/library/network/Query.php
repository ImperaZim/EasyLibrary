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
  public static function getServerInfo(string $ip, ?int $port = 19132): Promise {
    $url = "https://imperazim.cloud/plugins/EasyLibrary/query/";
    $postData = json_encode([
      'ip' => $ip,
      'port' => $port
    ]);

    $resolver = new PromiseResolver();
    $error = null;

    $response = Internet::postURL($url, $postData, 10, [], $error);

    if ($error !== null) {
      $resolver->reject(['error' => $error]);
    } elseif ($response instanceof InternetRequestResult) {
      $data = json_decode($response->getBody(), true);
      if (json_last_error() !== JSON_ERROR_NONE) {
        $resolver->reject(['error' => 'Invalid JSON response']);
      } else {
        $resolver->resolve($data);
      }
    } else {
      $resolver->reject(['error' => 'Unknown error']);
    }

    return $resolver->getPromise();
  }
}