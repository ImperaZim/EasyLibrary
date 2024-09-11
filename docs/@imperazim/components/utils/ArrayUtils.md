---

**New Feature: ArrayUtils Class**

We are excited to introduce the `ArrayUtils` class, which brings a suite of SQL-like operations to array manipulation in PHP. This class offers a versatile set of methods to perform queries, modifications, and data management tasks on arrays, mirroring common SQL commands. Below is a summary of the new functionalities:

### Features

- **`select(string $query, array $data): array`**

  - Performs a SQL-like `SELECT` query on the given array.
  - Supports filtering with `WHERE`, sorting with `ORDER BY`, and limiting results with `LIMIT`.
  - Example: `ArrayUtils::select("SELECT * FROM tasks WHERE id = '1'", $tasks)` returns the tasks with ID 1.

- **`insert(string $query, array &$data): array`**

  - Performs a SQL-like `INSERT` operation to add new items into the array.
  - Inserts a new item into the array based on the given query.
  - Example: `ArrayUtils::insert("INSERT INTO tasks (id, category, name, status) VALUES ('4', 'Leisure', 'Play', '1')", $tasks)` adds a new task to the array.

- **`update(string $query, array &$data): array`**

  - Performs a SQL-like `UPDATE` operation to modify existing items in the array.
  - Updates items that match the `WHERE` condition with new values specified in the query.
  - Example: `ArrayUtils::update("UPDATE tasks SET category = 'Updated Category' WHERE id = '1'", $tasks)` updates the category of tasks with ID 1.

- **`delete(string $query, array &$data): array`**

  - Performs a SQL-like `DELETE` operation to remove items from the array.
  - Deletes items that match the `WHERE` condition specified in the query.
  - Example: `ArrayUtils::delete("DELETE FROM tasks WHERE id = '3'", $tasks)` removes tasks with ID 3 from the array.

- **`query(string $query, array &$data): array|string`**

  - Executes a SQL-like query (SELECT, INSERT, UPDATE, DELETE) on the given array.
  - Automatically determines the type of query and performs the corresponding operation.
  - Example: `ArrayUtils::query("SELECT * FROM tasks WHERE id = '1'", $tasks)` performs a SELECT operation, while `ArrayUtils::query("DELETE FROM tasks WHERE id = '3'", $tasks)` performs a DELETE operation.

- **`merge(array $array1, array $array2): array`**

  - Merges two arrays into one, with the second array overwriting the first in case of key conflicts.
  - Example: `ArrayUtils::merge($array1, $array2)` combines `$array1` and `$array2`, with `$array2` values taking precedence.

- **`pluck(array $array, string $key): array`**

  - Extracts values for a specific key from an array of associative arrays.
  - Example: `ArrayUtils::pluck($array, 'name')` returns an array of values for the 'name' key from the associative arrays in `$array`.

- **`flatten(array $array): array`**

  - Flattens a multi-dimensional array into a single-dimensional array.
  - Example: `ArrayUtils::flatten($array)` converts a nested array into a flat array containing all values.

- **`groupBy(array $array, string $key): array`**

  - Groups an array of associative arrays by a specific key.
  - Example: `ArrayUtils::groupBy($array, 'category')` groups items in `$array` by the 'category' key.

### Usage Examples

- **SELECT Query:**

  - `ArrayUtils::select("SELECT * FROM tasks WHERE id = '1'", $tasks)` might return:
    ```php
    [
      [
        "id" => "1",
        "category" => "Work",
        "name" => "Task 1",
        "status" => "completed"
      ]
    ]
    ```

- **INSERT Query:**

  - `ArrayUtils::insert("INSERT INTO tasks (id, category, name, status) VALUES ('4', 'Leisure', 'Play', '1')", $tasks)` adds a new task:
    ```php
    [
      // existing tasks,
      [
        "id" => "4",
        "category" => "Leisure",
        "name" => "Play",
        "status" => "1"
      ]
    ]
    ```

- **UPDATE Query:**

  - `ArrayUtils::update("UPDATE tasks SET category = 'Updated Category' WHERE id = '1'", $tasks)` updates the category of tasks with ID 1.

- **DELETE Query:**

  - `ArrayUtils::delete("DELETE FROM tasks WHERE id = '3'", $tasks)` removes tasks with ID 3.

- **Query Method:**

  - `ArrayUtils::query("SELECT * FROM tasks WHERE id = '1'", $tasks)` executes a SELECT operation.
  - `ArrayUtils::query("INSERT INTO tasks (id, category, name, status) VALUES ('4', 'Leisure', 'Play', '1')", $tasks)` executes an INSERT operation.

- **Merge Arrays:**

  - `ArrayUtils::merge($array1, $array2)` merges two arrays with `$array2` overwriting `$array1` values in case of conflicts.

- **Pluck Values:**

  - `ArrayUtils::pluck($array, 'name')` extracts values associated with the 'name' key from the array of associative arrays.

- **Flatten Array:**

  - `ArrayUtils::flatten($array)` flattens a nested array into a single-dimensional array.

- **Group By Key:**

  - `ArrayUtils::groupBy($array, 'category')` groups the array items by the 'category' key.

The `ArrayUtils` class provides powerful functionality to handle array data with SQL-like operations and additional array manipulation features, making data management and manipulation more intuitive and flexible.

---