<?php

declare(strict_types = 1);

namespace imperazim\components\cache;

use imperazim\components\plugin\PluginToolkit;
use imperazim\components\plugin\PluginComponent;
use imperazim\components\plugin\traits\PluginComponentsTrait;

/**
* Class Cache
* @package imperazim\components\cache
*/
final class Cache extends PluginComponent {
  use PluginComponentsTrait;

  /**
  * @var array An associative array where keys represent cache keys and values represent cached data with expiration.
  */
  private static array $cache = [];

  /**
  * Initializes the Cache component.
  * @param PluginToolkit $plugin The Plugin.
  */
  public static function init(PluginToolkit $plugin): array {
    return [];
  }

  /**
  * Stores data in the cache with an optional time to live (TTL).
  * @param string $key The cache key.
  * @param mixed $value The value to be cached.
  * @param int|null $ttl Time to live in seconds. Null for no expiration.
  */
  public static function put(string $key, mixed $value, ?int $ttl = null): void {
    self::$cache[$key] = [
      'value' => $value,
      'expires_at' => $ttl !== null ? time() + $ttl : null
    ];
  }

  /**
  * Retrieves cached data by key, returning null if the data is expired or doesn't exist.
  * @param string $key The cache key.
  * @return mixed|null The cached value or null if expired/not found.
  */
  public static function get(string $key): mixed {
    if (!isset(self::$cache[$key])) {
      return null;
    }

    $cacheItem = self::$cache[$key];

    // Check if the item has expired
    if ($cacheItem['expires_at'] !== null && $cacheItem['expires_at'] < time()) {
      self::invalidate($key);
      return null;
    }

    return $cacheItem['value'];
  }

  /**
  * Invalidates a cached item, removing it from the cache.
  * @param string $key The cache key.
  */
  public static function invalidate(string $key): void {
    unset(self::$cache[$key]);
  }

  /**
  * Clears all cached data.
  */
  public static function clear(): void {
    self::$cache = [];
  }

  /**
  * Returns cache statistics such as total entries, hits, misses, and expired items.
  * @return array Cache statistics.
  */
  public static function getStats(): array {
    $totalEntries = count(self::$cache);
    $expiredEntries = 0;

    foreach (self::$cache as $key => $item) {
      if ($item['expires_at'] !== null && $item['expires_at'] < time()) {
        $expiredEntries++;
      }
    }

    return [
      'total_entries' => $totalEntries,
      'expired_entries' => $expiredEntries
    ];
  }
}