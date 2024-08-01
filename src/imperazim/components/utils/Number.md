**New Feature: Number Class**

We are excited to introduce the `Number` class, a versatile utility for various numerical operations. This class includes a range of functions designed to simplify number formatting, conversion, and manipulation. Below is a summary of the new functionalities:

#### Features

- **`formatAsCurrency($number, int $decimals = 0, string $decimalSeparator = ',', string $thousandSeparator = '.'): string`**

  - Converts a number to a formatted currency string.
  - Supports customization of decimal places, decimal separator, and thousand separator.
  - Example: `10000` => `10.000`.

- **`formatAsStatistic($number): string`**

  - Converts a number to a statistical shorthand string.
  - Uses suffixes like K (thousand), M (million), B (billion), etc.
  - Example: `10000` => `10K`.

- **`format(int $length, string|int $number): string`**

  - Formats a given ID as a string with leading zeros if the input is numeric.
  - Example: `format(5, 123)` => `00123`.

- **`formatAsPercentage(float $number, int $decimals = 2): string`**

  - Converts a number to a formatted percentage string.
  - Supports customization of decimal places.
  - Example: `0.85` => `85%`.

- **`roundToDecimals(float $number, int $decimals = 2): float`**

  - Rounds a number to a specified number of decimal places.
  - Example: `roundToDecimals(2.3456, 2)` => `2.35`.

- **`max(array $numbers)`**

  - Gets the maximum number from an array of numbers.
  - Returns `null` if the array is empty.

- **`min(array $numbers)`**

  - Gets the minimum number from an array of numbers.
  - Returns `null` if the array is empty.

- **`random(int $min = 0, int $max = PHP_INT_MAX): int`**
  - Generates a random number within a specified range.
  - Example: `random(1, 10)` might return any integer between 1 and 10.

#### Usage Examples

- **Currency Formatting:**

  - `Number::formatAsCurrency(1234567.89, 2, '.', ',')` returns `1,234,567.89`.

- **Statistical Formatting:**

  - `Number::formatAsStatistic(1234567)` returns `1.2M`.

- **ID Formatting:**

  - `Number::format(6, 123)` returns `000123`.

- **Percentage Formatting:**

  - `Number::formatAsPercentage(0.857)` returns `85.70%`.

- **Rounding:**

  - `Number::roundToDecimals(3.14159, 3)` returns `3.142`.

- **Maximum and Minimum:**

  - `Number::max([1, 2, 3, 4, 5])` returns `5`.
  - `Number::min([1, 2, 3, 4, 5])` returns `1`.

- **Random Number Generation:**
  - `Number::random(1, 100)` might return any integer between 1 and 100.

These new functionalities in the `Number` class provide enhanced control and flexibility for managing numerical operations, enabling more efficient and precise data handling capabilities.
