<?php

declare(strict_types = 1);

namespace imperazim\components\database;

use PDO;
use PDOException;

/**
* Class Sqlite3
* @package imperazim\components\database
*/
final class Sqlite3 {

  /** @var PDO|null */
  private ?PDO $sqlite = null;

  /**
  * Sqlite3 constructor.
  * @param string $directory The directory of the file.
  * @param string $fileName The name of the file.
  * @param bool|null $autoGenerate Whether to generate the file if it does not exist.
  */
  public function __construct(
    private ?string $directory,
    private ?string $fileName
  ) {
    $directory = rtrim(str_replace('//', '/', $directory . '/'), '/') . '/';
    try {
      $this->sqlite = new PDO('sqlite:' . $directory . $fileName . '.db', null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
      ]);
    } catch (PDOException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Creates tables if they do not exist.
  * @param array $tables An associative array where the key is the table name and the value is the SQL query to create the table.
  * @return void
  */
  public function createTableIfNotExists(array $tables): void {
    try {
      foreach ($tables as $table => $rows) {
        $rows = implode(", ", array_keys($rows));
        $this->sqlite->exec("CREATE TABLE IF NOT EXISTS $table ($rows)");
      }
    } catch (PDOException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Inserts data into a specified table.
  * @param string $table The name of the table to insert data into.
  * @param array $data An associative array where the key is the column name and the value is the value to insert.
  * @return void
  */
  public function insert(string $table, array $data): void {
    try {
      $columns = implode(", ", array_keys($data));
      $placeholders = implode(", ", array_fill(0, count($data), "?"));
      $values = array_values($data);

      $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
      $stmt = $this->sqlite->prepare($sql);
      $stmt->execute($values);
    } catch (PDOException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Selects data from a specified table with optional filters.
  * @param string $table The name of the table to select data from.
  * @param string $column The column(s) to select.
  * @param array $filters An array of associative arrays for filtering the results.
  * @return array The selected data.
  */
  public function select(string $table, string $column, array $filters = []): array {
    try {
      $sql = "SELECT $column FROM $table";
      $values = [];

      if (!empty($filters)) {
        $sql .= " WHERE " . $this->buildWhereClause($filters, $values);
      }

      $stmt = $this->sqlite->prepare($sql);
      $stmt->execute($values);
      return $stmt->fetchAll();
    } catch (PDOException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Updates data in a specified table.
  * @param string $table The name of the table to update data in.
  * @param string $column The column to update.
  * @param mixed $value The new value to set.
  * @param array $filters An array of associative arrays for filtering the rows to update.
  * @return bool Whether any rows were updated.
  */
  public function update(string $table, string $column, $value, array $filters = []): bool {
    try {
      $sql = "UPDATE $table SET $column = ?";
      $values = [$value];
      if (!empty($filters)) {
        $sql .= " WHERE " . $this->buildWhereClause($filters, $values);
      }
      $stmt = $this->sqlite->prepare($sql);
      $stmt->execute($values);
      return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Checks if a record exists in a specified table.
  * @param string $table The name of the table to check.
  * @param array $conditions An array of associative arrays for the conditions to check.
  * @return bool Whether the record exists.
  */
  public function exists(string $table, array $conditions): bool {
    try {
      $sql = "SELECT COUNT(*) AS count FROM $table WHERE ";
      $values = [];

      $sql .= $this->buildWhereClause($conditions, $values);

      $stmt = $this->sqlite->prepare($sql);
      $stmt->execute($values);
      $row = $stmt->fetch();
      return $row['count'] > 0;
    } catch (PDOException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Builds a WHERE clause from an array of conditions.
  * @param array $filters An array of associative arrays for filtering the results.
  * @param array $values Reference to the values array to be used in the prepared statement.
  * @return string The WHERE clause.
  */
  private function buildWhereClause(array $filters, array &$values): string {
    $whereClauses = [];
    foreach ($filters as $filter) {
      $clauses = [];
      foreach ($filter as $key => $value) {
        $clauses[] = "$key = ?";
        $values[] = $value;
      }
      $whereClauses[] = implode(" AND ", $clauses);
    }
    return implode(" AND ", $whereClauses);
  }
}