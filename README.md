# Homeowner Parser

A Laravel application for parsing homeowner information from various string formats into structured data.

## Overview

The Homeowner Parser is designed to parse strings containing homeowner information into a structured format. It can handle various formats including:

- Single homeowners (e.g., "Mr John Smith")
- Multiple homeowners with different separators (e.g., "Mr John Smith and Mrs Jane Doe")
- Special patterns like "Mr and Mrs Smith"
- Names with initials, hyphenated surnames, and various title formats

The system uses a pattern-matching approach with a collection of specialized parsers that each handle specific formats. It's designed to be extensible, allowing new patterns to be added easily.

## Getting Started

### Prerequisites

- PHP 8.1 or higher
- Composer
- Laravel 10.x

### Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   ```
3. Copy the `.env.example` file to `.env` and configure your environment

## Usage

### Processing a CSV File

The application includes a command to process a CSV file containing homeowner information:

```bash
php artisan app:process-csv
```

This command expects a CSV file named `streetgroup.csv` in the `storage/app/public` directory with a header row and a column named `homeowner`.

Example CSV format:
```
homeowner
Mr John Smith
Mrs Jane Doe
Mr and Mrs Brown
```

The command will:
1. Parse each homeowner string
2. Display a progress bar during processing
3. Show error messages for entries that couldn't be parsed
4. Provide a summary of successful and failed parsing attempts

### Using the Homeowner Parser in Code

You can use the Homeowner parser in your own code:

```php
use App\Homeowners\Homeowner;
use App\Homeowners\Parser\Exceptions\StringCouldNotBeParsedException;

// Resolve from the container
$homeowner = app(Homeowner::class);

try {
    // Parse a homeowner string into an array of arrays
    $result = $homeowner->parseHomeownerStringToArray('Mr John Smith');
    
    // $result will be:
    // [
    //     [
    //         'title' => 'Mr',
    //         'first_name' => 'John',
    //         'last_name' => 'Smith',
    //         'initial' => null
    //     ]
    // ]
} catch (StringCouldNotBeParsedException $e) {
    // Handle parsing error
    echo "Error parsing homeowner: " . $e->getMessage();
}
```

## Design Choices

### Architecture

The parser uses a Chain of Responsibility pattern where each pattern in the chain attempts to parse the input string. The first pattern that matches handles the string.

Key components:

1. **Homeowner Class**: The main entry point that returns arrays of parsed homeowner data
2. **ArrayParser**: Manages the collection of patterns and delegates to them
3. **Pattern Classes**: Individual parsers for specific formats (e.g., `SingleHomeownerPattern`, `MrAndMrsPattern`)
4. **Person Class**: Internal representation of a homeowner with properties for title, first name, last name, and initial

### Exception Handling

The system uses a custom exception (`StringCouldNotBeParsedException`) to handle parsing errors. This provides clear error messages and allows for graceful error handling in the console command.

### Encapsulation

The `Person` class is used internally but is not exposed outside the Homeowner context. Instead, the public API returns arrays, maintaining a clean separation between internal implementation and external interfaces.

## Extending the Parser

### Adding a New Pattern

To add a new pattern:

1. Create a new class that implements the `PatternInterface`:

```php
<?php

namespace App\Homeowners\Parser\Pattern;

use App\Homeowners\Parser\Contract\PatternInterface;
use App\Homeowners\Parser\Exceptions\StringCouldNotBeParsedException;
use App\Homeowners\Person;

class MyNewPattern implements PatternInterface
{
    public function matches(string $string): bool
    {
        // Implement logic to determine if this pattern matches the string
        return preg_match('/your-pattern-regex/', $string) === 1;
    }

    public function handle(string $string): array
    {
        // If the string doesn't match, throw an exception
        if (!$this->matches($string)) {
            throw new StringCouldNotBeParsedException("String does not match MyNewPattern");
        }

        // Parse the string and return an array of Person objects
        // Example:
        return [
            new Person('Mr', 'John', 'Smith', null)
        ];
    }
}
```

2. Register your pattern in the `ParserServiceProvider`:

```php
<?php

namespace App\Homeowners\Parser;

use App\Homeowners\Parser\Pattern\MyNewPattern;
use Illuminate\Support\ServiceProvider;

class ParserServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register existing patterns...
        
        // Register your new pattern
        $this->app->tag([
            MyNewPattern::class,
            // Other patterns...
        ], 'homeowner-patterns');
    }
}
```

### Changing the Parser Implementation

If you need to use a different parser implementation:

1. Create a new parser that implements the required interface
2. Update the binding in `ParserServiceProvider`:

```php
<?php

namespace App\Homeowners\Parser;

use App\Homeowners\Parser\Contract\ParserInterface;
use App\Homeowners\Parser\MyNewParser;
use Illuminate\Support\ServiceProvider;

class ParserServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind your new parser implementation
        $this->app->bind(ParserInterface::class, MyNewParser::class);
        
        // Register patterns...
    }
}
```

## Testing

The application includes both unit and feature tests:

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter=HomeownerTest
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
