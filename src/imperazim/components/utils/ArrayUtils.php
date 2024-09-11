<?php

declare(strict_types = 1);

namespace imperazim\components\utils;

/**
* Class ArrayUtils
* @package imperazim\components\utils
*/
final class ArrayUtils {

  /**
   * Perform a SQL-like SELECT query on the given array.
   *
   * @param string $query The SQL-like query string.
   * @param array $data The array of data to perform the query on.
   * @return array The resulting data after performing the SELECT query.
   */
  public static function select(string $query, array $data): array {
    if (preg_match("/SELECT\s+(\*|\w+(?:,\s*\w+)*)\s+FROM\s+(\w+)\s*(WHERE\s+(.+))?\s*(ORDER BY\s+(\w+)\s*(ASC|DESC)?)?\s*(LIMIT\s+(\d+))?/i", $query, $matches)) {
      $columns = $matches[1];
      $table = $matches[2];
      $whereClause = $matches[4] ?? null;
      $orderBy = $matches[6] ?? null;
      $orderDirection = $matches[7] ?? 'ASC';
      $limit = $matches[9] ?? null;

      // Retrieve data from the correct table
      if (!isset($data[$table])) {
        return [];
      }
      $result = $data[$table];

      // WHERE clause
      if ($whereClause) {
        $result = self::applyWhereClause($result, $whereClause);
      }

      // ORDER BY clause
      if ($orderBy) {
        usort($result, fn($a, $b) => $orderDirection === 'DESC'
          ? strcmp($b[$orderBy], $a[$orderBy])
          : strcmp($a[$orderBy], $b[$orderBy])
        );
      }

      // LIMIT clause
      if ($limit) {
        $result = array_slice($result, 0, (int)$limit);
      }

      // Selecting specific columns or all (*)
      if ($columns !== '*') {
        $fields = array_map('trim', explode(',', $columns));
        $result = array_map(fn($item) => array_intersect_key($item, array_flip($fields)), $result);
      }

      return $result;
    }

    return [];
  }

  /**
   * Apply the WHERE clause logic to filter the data.
   *
   * @param array $data The data to filter.
   * @param string $whereClause The WHERE clause condition.
   * @return array Filtered data.
   */
  private static function applyWhereClause(array $data, string $whereClause): array {
    $operators = ['=', '!=', '>', '<', '>=', '<='];
    foreach ($operators as $operator) {
      if (strpos($whereClause, $operator) !== false) {
        [$field, $value] = explode($operator, $whereClause);
        $field = trim($field);
        $value = trim($value, " '");

        return array_filter($data, function($item) use ($field, $value, $operator) {
          if (!isset($item[$field])) {
            return false;
          }
          switch ($operator) {
            case '=':
              return $item[$field] == $value;
            case '!=':
              return $item[$field] != $value;
            case '>':
              return $item[$field] > $value;
            case '<':
              return $item[$field] < $value;
            case '>=':
              return $item[$field] >= $value;
            case '<=':
              return $item[$field] <= $value;
          }
          return false;
        });
      }
    }

    return $data;
  }

  /**
  * Perform a SQL-like INSERT query on the given array.
  *
  * @param string $query The SQL-like INSERT query string.
  * @param array &$data The array of data to insert into.
  * @return array The updated data after performing the INSERT.
  */
  public static function insert(string $query, array &$data): array {
    if (preg_match("/INSERT INTO\s+(\w+)\s+\((.+)\)\s+VALUES\s+\((.+)\)/i", $query, $matches)) {
      $fields = array_map('trim', explode(',', $matches[2]));
      $values = array_map(fn($value) => trim($value, " '"), explode(',', $matches[3]));

      $newItem = array_combine($fields, $values);
      $data[] = $newItem;
    }

    return $data;
  }

  /**
  * Perform a SQL-like UPDATE query on the given array.
  *
  * @param string $query The SQL-like UPDATE query string.
  * @param array &$data The array of data to update.
  * @return array The updated data after performing the UPDATE.
  */
  public static function update(string $query, array &$data): array {
    if (preg_match("/UPDATE\s+(\w+)\s+SET\s+(.+)\s+WHERE\s+(.+)/i", $query, $matches)) {
      $setClauses = explode(',', $matches[2]);
      $whereClause = $matches[3];

      [$field,
        $value] = explode('=', $whereClause);
      $field = trim($field);
      $value = trim($value, " '");

      $updates = [];
      foreach ($setClauses as $clause) {
        [$key,
          $val] = explode('=', $clause);
        $updates[trim($key)] = trim($val, " '");
      }

      foreach ($data as &$item) {
        if ($item[$field] == $value) {
          $item = array_merge($item, $updates);
        }
      }
    }

    return $data;
  }

  /**
  * Perform a SQL-like DELETE query on the given array.
  *
  * @param string $query The SQL-like DELETE query string.
  * @param array &$data The array of data to delete from.
  * @return array The updated data after performing the DELETE.
  */
  public static function delete(string $query, array &$data): array {
    if (preg_match("/DELETE FROM\s+(\w+)\s+WHERE\s+(.+)/i", $query, $matches)) {
      $whereClause = $matches[2];

      [$field,
        $value] = explode('=', $whereClause);
      $field = trim($field);
      $value = trim($value, " '");

      $data = array_filter($data, fn($item) => $item[$field] != $value);
    }

    return $data;
  }

  /**
  * Perform a SQL-like query (SELECT, INSERT, UPDATE, DELETE) on the given array.
  *
  * @param string $query The SQL-like query string.
  * @param array &$data The array of data to perform the query on.
  * @return array|string The resulting data after performing the query, or an error message.
  */
  public static function query(string $query, array &$data): array|string {
    if (str_starts_with(strtoupper($query), 'SELECT')) {
      return self::select($query, $data);
    } elseif (str_starts_with(strtoupper($query), 'INSERT')) {
      return self::insert($query, $data);
    } elseif (str_starts_with(strtoupper($query), 'UPDATE')) {
      return self::update($query, $data);
    } elseif (str_starts_with(strtoupper($query), 'DELETE')) {
      return self::delete($query, $data);
    } else {
      return "Invalid query!";
    }
  }

  /**
  * Merge two arrays into one, with the second array overwriting the first in case of key conflicts.
  *
  * @param array $array1 The first array.
  * @param array $array2 The second array, which will overwrite the first array's values in case of conflicts.
  * @return array The merged array.
  */
  public static function merge(array $array1, array $array2): array {
    return array_merge($array1, $array2);
  }

  /**
  * Extract values for a specific key from an array of associative arrays.
  *
  * @param array $array The array of associative arrays.
  * @param string $key The key to extract values for.
  * @return array An array of values for the specified key.
  */
  public static function pluck(array $array, string $key): array {
    return array_column($array, $key);
  }

  /**
  * Flatten a multi-dimensional array into a single-dimensional array.
  *
  * @param array $array The multi-dimensional array to flatten.
  * @return array The flattened array.
  */
  public static function flatten(array $array): array {
    $result = [];
    array_walk_recursive($array, fn($value) => $result[] = $value);
    return $result;
  }

  /**
  * Group an array of associative arrays by a specific key.
  *
  * @param array $array The array of associative arrays.
  * @param string $key The key to group by.
  * @return array An array of arrays grouped by the specified key.
  */
  public static function groupBy(array $array, string $key): array {
    $result = [];
    foreach ($array as $item) {
      $result[$item[$key]][] = $item;
    }
    return $result;
  }
}