<?php

declare(strict_types = 1);

namespace imperazim\components\database;

use mysqli;
use mysqli_sql_exception;
use imperazim\components\database\exception\DatabaseException;

/**
* Class Mysql
* @package imperazim\components\database
*/
final class Mysql {

  /** @var mysqli */
  private mysqli $connection;

  /**
  * Mysql constructor.
  * @param mysqli|null $connection The mysqli connection.
  */
  public function __construct(private ?mysqli $connection) {}

  /**
  * Connect to a MySQL database.
  * @param string $host The database host.
  * @param string $user The database user.
  * @param string $password The database password.
  * @param string $database The database name.
  * @return self A new instance of Mysql.
  * @throws DatabaseException If the connection fails.
  */
  public static function connect(string $host, string $user, string $password, string $database): self {
    try {
      $connection = new mysqli($host, $user, $password, $database);
      if ($connection->connect_error) {
        throw new DatabaseException("Connection failed: " . $connection->connect_error);
      }
      return new self(connection: $connection);
    } catch (mysqli_sql_exception $e) {
      new \crashdump($e);
    }
  }

  /**
  * Execute an SQL query and return the results.
  * @param string $query The SQL query to execute.
  * @return array The results of the query.
  * @throws DatabaseException If the query fails.
  */
  public function query(string $query): array {
    try {
      $result = $this->connection->query($query);
      if ($this->connection->error) {
        throw new DatabaseException("Query failed: " . $this->connection->error);
      }
      return $result->fetch_all(MYSQLI_ASSOC);
    } catch (mysqli_sql_exception $e) {
      new \crashdump($e);
    }
  }
}