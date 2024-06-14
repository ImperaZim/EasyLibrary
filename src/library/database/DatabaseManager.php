<?php

declare(strict_types = 1);

namespace library\database;

use mysqli;
use mysqli_sql_exception;
use library\plugin\PluginToolkit;

final class DatabaseManager {

  /**
  * DatabaseManager constructor.
  */
  public function __construct(private PluginToolkit $plugin) {}

  /**
  * Connect to a MySQL database.
  * @param string $host
  * @param string $user
  * @param string $password
  * @param string $database
  * @throws mysqli_sql_exception
  */
  private function setConnection(string $host, string $user, string $password, string $database): void {
    $this->plugin->connection = new mysqli($host, $user, $password, $database);
    if ($this->getConnection()->connect_error) {
      throw new mysqli_sql_exception("Connection failed: " . $this->getConnection()->connect_error);
    }
  }

  /**
  * Execute an SQL query and return the results.
  * @param string $query
  * @return array
  * @throws mysqli_sql_exception
  */
  public function query(string $query): array {
    $result = $this->getConnection()->query($query);
    if ($this->getConnection()->error) {
      throw new mysqli_sql_exception("Query failed: " . $this->connection->error);
    }
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  /**
  * Get the mysqli connection object.
  * @return mysqli
  */
  public function getConnection(): mysqli {
    return $this->plugin->connection;
  }
  
}