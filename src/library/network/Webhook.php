<?php

declare(strict_types = 1);

namespace library\network;

/**
* Class Webhook
* @package library\network
*/
final class Webhook {

  /**
  * Send a Webhook.
  * @param string $link
  * @param array|null $data
  * @return bool
  */
  public static function sendWebhook(
    string $link,
    ?array $data = []
  ): bool {
    try {
      $channel = curl_init($link);
      curl_setopt_array($channel, [
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
      ]);
      $response = curl_exec($channel);
      $httpCode = curl_getinfo($channel, CURLINFO_HTTP_CODE);
      curl_close($channel);
      return $httpCode >= 200 && $httpCode < 300;
    } catch (\Throwable $e) {
      var_dump($e);
    }
  }
}