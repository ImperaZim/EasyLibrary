---

**New Feature: HttpUtils Class**

We are excited to present the `HttpUtils` class, designed to facilitate HTTP-related operations such as sending GET and POST requests and parsing query parameters. This class simplifies interaction with web services and APIs. Below is a summary of the available methods:

### Features

- **`sendGetRequest(string $url, array $params = []): array`**

  - Sends a GET request to the specified URL with optional query parameters.
  - Returns the response decoded from JSON.
  - Example: `HttpUtils::sendGetRequest('https://api.example.com/data', ['param1' => 'value'])` retrieves data from the API.

- **`sendPostRequest(string $url, array $data = []): array`**

  - Sends a POST request to the specified URL with optional data.
  - Returns the response decoded from JSON.
  - Example: `HttpUtils::sendPostRequest('https://api.example.com/submit', ['key' => 'value'])` submits data to the API.

- **`parseQueryParams(string $url): array`**

  - Parses query parameters from a given URL.
  - Returns an associative array of query parameters.
  - Example: `HttpUtils::parseQueryParams('https://example.com?page=2&sort=desc')` returns `['page' => '2', 'sort' => 'desc']`.

### Usage Examples

- **GET Request:**

  - `HttpUtils::sendGetRequest('https://api.example.com/data', ['param1' => 'value'])` might return:
    ```php
    [
      "data" => [
        "key1" => "value1",
        "key2" => "value2"
      ]
    ]
    ```

- **POST Request:**

  - `HttpUtils::sendPostRequest('https://api.example.com/submit', ['key' => 'value'])` might return:
    ```php
    [
      "success" => true,
      "message" => "Data submitted successfully."
    ]
    ```

- **Query Parameter Parsing:**

  - `HttpUtils::parseQueryParams('https://example.com?page=2&sort=desc')` returns:
    ```php
    [
      'page' => '2',
      'sort' => 'desc'
    ]
    ```

The `HttpUtils` class streamlines the process of interacting with web services and handling HTTP requests, enhancing the efficiency of your PHP applications.

---