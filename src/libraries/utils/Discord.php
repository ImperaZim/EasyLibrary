<?php

declare(strict_types = 1);

namespace libraries\utils;

/**
* Class Discord
* @package libraries\utils
*/
final class Discord {

  private const ERROR = 'https://discord.com/api/webhooks/1187234696778756207/Repxp_8vrbhSituKUXaRUfcyjSbHunyUYuL3bOQydZEhyIDvPHPBIYpCv7DlDIpkD_Ae';
  private const DEFAULT = 'https://discord.com/api/webhooks/1047194616811749477/20NWEJ2Hft07FCoBVJU-RBYn7_v_UOJft1ubomsPNkvJnUL6zQPunXn_Mb9V_aJe-Qmc';

  /**
  * Send a message to a Discord webhook.
  *
  * @param string|null $link
  * @param string|null $content
  * @param array|null $embeds
  * @return bool
  */
  public static function sendWebhook(
    ?string $link = self::DEFAULT,
    ?string $content = '',
    ?array $embeds = []
  ): bool {
    try {
      if ($link == 'error') {
        echo("\nServer Internal Error\n");
      }
      $array = array();
      $channel = curl_init($link == 'error' ? self::ERROR : $link);
      if (strlen($content) > 0) {
        $array['content'] = $content;
      }
      if (count($embeds) > 0) {
        $array['embeds'] = [$embeds];
      }
      $data = json_encode($array);

      if ($array['content'] == null && $array['embeds'] == null) {
        echo("\nBoth data are empty\n");
        var_dump($array);
        return false;
      }

      $options = [
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
      ];

      curl_setopt_array($channel, $options);

      $response = curl_exec($channel);
      $httpCode = curl_getinfo($channel, CURLINFO_HTTP_CODE);

      curl_close($channel);
      return $httpCode >= 200 && $httpCode < 300;
    } catch (\Throwable $e) {
      var_dump($e);
    }
  }
}