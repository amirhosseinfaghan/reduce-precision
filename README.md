```markdown
# NumberFormatter

A PHP package for formatting numbers with various templates, precision levels, and output formats.

## Features

- Supports multiple languages (e.g., English, Persian)
- Flexible templates (number, USD, IRT, IRR, percent)
- Customizable precision (high, medium, low, auto)
- Various output formats (plain, HTML, Markdown)
- Handles E-notation and converts to regular numbers

## Installation

You can install the package via Composer:

```sh
composer require amirhosseinfaghan/reduce-precision
```

## Usage

### Basic Usage

```php
require 'vendor/autoload.php';

use NumberFormatter\NumberFormatter;

$formatter = new NumberFormatter();
echo $formatter->toString(12345.678); // Default format
```

### Setting Language

```php
$formatter->setLanguage('fa');
echo $formatter->toString(12345.678); // Output in Persian
```

### Setting Template and Precision

```php
$formatter->setTemplate('usd', 'high');
echo $formatter->toString(12345.678); // Output in USD format with high precision
```

### Output Formats

```php
echo $formatter->toPlainString(12345.678); // Plain text output
echo $formatter->toHtmlString(12345.678);  // HTML formatted output
echo $formatter->toMdString(12345.678);    // Markdown formatted output
```

## Methods

### setLanguage

Sets the language for the formatter.

**Parameters:**
- `lang` (string): The language code (e.g., 'en', 'fa').
- `config` (array): Optional configuration for markers and prefixes/postfixes.

**Returns:**
- `NumberFormatter`: The formatter instance.

### setTemplate

Sets the template and precision for formatting.

**Parameters:**
- `template` (string): The template type (number, usd, irt, irr, percent).
- `precision` (string): The precision level (high, medium, low, auto).

**Returns:**
- `NumberFormatter`: The formatter instance.

### toString

Formats the input number as a string.

**Parameters:**
- `input` (mixed): The number to format.

**Returns:**
- `string`: The formatted number.

### toPlainString

Formats the input number as a plain text string.

**Parameters:**
- `input` (mixed): The number to format.

**Returns:**
- `string`: The formatted number.

### toHtmlString

Formats the input number as an HTML string.

**Parameters:**
- `input` (mixed): The number to format.

**Returns:**
- `string`: The formatted number.

### toMdString

Formats the input number as a Markdown string.

**Parameters:**
- `input` (mixed): The number to format.

**Returns:**
- `string`: The formatted number.

## Testing

### PHP Unit Tests

You can run the tests using PHPUnit. Make sure you have PHPUnit installed.

```sh
./vendor/bin/phpunit tests
```

### JavaScript Test Example

A simple JavaScript test can be used to compare the results.

```js
const formatter = new NumberFormatter();
console.log(formatter.toString(12345.678)); // Expected output
```

## Contributing

Contributions are welcome! Please submit a pull request or open an issue to discuss your ideas.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgements

Special thanks to all contributors and supporters.

## Repository

For more information, visit the [GitHub repository](https://github.com/amirhosseinfaghan/reduce-precision).
```

This `README.md` file provides a comprehensive overview and usage instructions for your package, and includes the link to your GitHub repository.
