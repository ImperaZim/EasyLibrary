**New Feature: Text Class**

We are thrilled to announce the introduction of the `Text` class, a comprehensive utility for various text manipulation operations. This class includes a range of functions designed to streamline text formatting, cleaning, and transformation. Below is a summary of the new functionalities:

### Features

- **`insertLineBreaks(string $text, int $maxLength = 36): string`**

  - Inserts new lines every specified number of characters without splitting words.
  - Ensures words are not split in the middle.
  - Example: `insertLineBreaks('This is a long piece of text that needs line breaks.', 10)`.

- **`stripHtmlTags(string $text): string`**

  - Removes all HTML tags from the input text.
  - Example: `stripHtmlTags('<p>This is a paragraph.</p>')` returns `This is a paragraph.`.

- **`removeExtraSpaces(string $text): string`**

  - Removes extra spaces and trims the text.
  - Example: `removeExtraSpaces('  This  is   spaced text.  ')` returns `This is spaced text.`.

- **`toUpperCase(string $text): string`**

  - Converts the input text to uppercase.
  - Example: `toUpperCase('This is a test.')` returns `THIS IS A TEST.`.

- **`toLowerCase(string $text): string`**

  - Converts the input text to lowercase.
  - Example: `toLowerCase('THIS IS A TEST.')` returns `this is a test.`.

- **`truncate(string $text, int $maxLength = 100): string`**

  - Truncates the text to a specified length, adding ellipsis if needed.
  - Example: `truncate('This is a long text that needs to be truncated.', 20)` returns `This is a long te...`.

- **`removeSpecialChars(string $text): string`**
  - Removes special characters from the text.
  - Example: `removeSpecialChars('Hello, World!')` returns `Hello World`.

### Usage Examples

- **Line Break Insertion:**

  - `Text::insertLineBreaks('This is a long piece of text that needs line breaks.', 10)` might return:
    ```
    This is a
    long piece
    of text
    that needs
    line
    breaks.
    ```

- **HTML Tag Stripping:**

  - `Text::stripHtmlTags('<div><p>Text with HTML</p></div>')` returns `Text with HTML`.

- **Extra Space Removal:**

  - `Text::removeExtraSpaces('  Text   with  extra   spaces. ')` returns `Text with extra spaces.`.

- **Uppercase Conversion:**

  - `Text::toUpperCase('uppercase text')` returns `UPPERCASE TEXT`.

- **Lowercase Conversion:**

  - `Text::toLowerCase('LOWERCASE TEXT')` returns `lowercase text`.

- **Text Truncation:**

  - `Text::truncate('This text is too long and needs to be cut short.', 25)` returns `This text is too long...`.

- **Special Character Removal:**
  - `Text::removeSpecialChars('Special $chars@!#')` returns `Special chars`.

These new functionalities in the `Text` class provide powerful tools for managing and transforming text, ensuring your text handling needs are met with precision and ease.
