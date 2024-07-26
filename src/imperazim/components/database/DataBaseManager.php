<?php

declare(strict_types = 1);

namespace imperazim\components\database;

use imperazim\components\database\exception\DatabaseException;

/**
* Class DatabaseManager
* @package imperazim\components\database
*/
final class DatabaseManager {

  /**
  * Connects to the specified database using the provided configuration.
  * @param string $type The type of database (e.g., 'mysql', 'sqlite').
  * @param array $config The configuration array for the database connection.
  * @return mixed The database instance.
  * @throws PluginException If the connection fails or the configuration is invalid.
  */
  public static function connect(string $type, array $config): mixed {
    try {
      switch (strtolower($type)) {
        case 'mysql':
          return Mysql::connect($config['host'], $config['username'], $config['password'], $config['database']);
        case 'sqlite':
          return Sqlite3::connect($config['database']);
        default:
          throw new PluginException("Unsupported database type: $type");
      }
    } catch (\Exception $e) {
      throw new PluginException("Failed to connect to the database: " . $e->getMessage(), 0, $e);
    }
  }
}