<?php

declare(strict_types = 1);

namespace imperazim\components\utils;

/**
* Class HttpUtils
* @package imperazim\components\utils
*/
final class HttpUtils {

  /**
  * Sends a GET request and returns the response.
  * @param string $url The URL to send the GET request to.
  * @param array $params Optional query parameters to include in the request.
  * @return array The response from the GET request.
  * @throws Exception If the request fails.
  */
  public static function sendGetRequest(string $url, array $params = []): array {
    $query = http_build_query($params);
    $fullUrl = $query ? "{$url}?{$query}" : $url;

    $response = file_get_contents($fullUrl);
    if ($response === false) {
      throw new Exception("Failed to retrieve data from {$fullUrl}");
    }

    return json_decode($response, true);
  }

  /**
  * Sends a POST request and returns the response.
  * @param string $url The URL to send the POST request to.
  * @param array $data Optional data to include in the POST request.
  * @return array The response from the POST request.
  * @throws Exception If the request fails.
  */
  public static function sendPostRequest(string $url, array $data = []): array {
    $options = [
      'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data),
      ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    if ($response === false) {
      throw new Exception("Failed to post data to {$url}");
    }

    return json_decode($response, true);
  }

  /**
  * Parses query parameters from a URL.
  * @param string $url The URL to parse query parameters from.
  * @return array An associative array of query parameters.
  */
  public static function parseQueryParams(string $url): array {
    $query = parse_url($url, PHP_URL_QUERY);
    parse_str($query, $params);

    return $params;
  }
}