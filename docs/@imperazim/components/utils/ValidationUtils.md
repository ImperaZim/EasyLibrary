---

**New Feature: ValidationUtils Class**

We are pleased to introduce the `ValidationUtils` class, which provides a robust set of methods for validating various types of input data. This class is designed to streamline common validation tasks and ensure data integrity in your applications. Below is an overview of the new functionalities:

### Features

- **`isEmail(string $email): bool`**

  - Validates whether the provided string is a valid email address.
  - Example: `ValidationUtils::isEmail('example@example.com')` returns `true` if the email is valid.

- **`isPhoneNumber(string $phone): bool`**

  - Validates whether the provided string is a valid phone number.
  - Example: `ValidationUtils::isPhoneNumber('+1234567890')` returns `true` if the phone number matches the pattern.

- **`isUrl(string $url): bool`**

  - Validates whether the provided string is a valid URL.
  - Example: `ValidationUtils::isUrl('https://example.com')` returns `true` if the URL is valid.

- **`isInteger(string $value): bool`**

  - Checks if the provided string can be interpreted as an integer.
  - Example: `ValidationUtils::isInteger('123')` returns `true` if the value is an integer.

### Usage Examples

- **Email Validation:**

  - `ValidationUtils::isEmail('test@example.com')` returns `true`.

- **Phone Number Validation:**

  - `ValidationUtils::isPhoneNumber('+1234567890')` returns `true`.

- **URL Validation:**

  - `ValidationUtils::isUrl('https://example.com')` returns `true`.

- **Integer Check:**

  - `ValidationUtils::isInteger('123')` returns `true`.

The `ValidationUtils` class offers essential validation functionalities to ensure the accuracy and correctness of input data across your PHP applications.

---