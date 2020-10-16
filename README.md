# DTO

[![Latest Stable Version](https://img.shields.io/packagist/v/romanzipp/DTO.svg?style=flat-square)](https://packagist.org/packages/romanzipp/dto)
[![Total Downloads](https://img.shields.io/packagist/dt/romanzipp/DTO.svg?style=flat-square)](https://packagist.org/packages/romanzipp/dto)
[![License](https://img.shields.io/packagist/l/romanzipp/DTO.svg?style=flat-square)](https://packagist.org/packages/romanzipp/dto)
[![GitHub Build Status](https://img.shields.io/github/workflow/status/romanzipp/DTO/Tests?style=flat-square)](https://github.com/romanzipp/DTO/actions)

A strongly typed Data Transfer Object without magic for PHP 7.4+

## Installation

```
composer require romanzipp/dto
```

- For **PHP 7.4** please use [`1.x`](https://packagist.org/packages/romanzipp/dto#1.0.0)
- For **PHP 8.0** please use [`2.x`](https://packagist.org/packages/romanzipp/dto#2.0.0) (coming soon)

## Usage

```php
use romanzipp\DTO\AbstractData;

class DummyData extends AbstractData
{
    protected static array $required = [
        'name',
        'stuff',
    ];

    public string $name;

    public ?string $nickname;

    public $stuff;

    public DateTime $birthday;

    public bool $subscribeNewsletter = false;
}

$data = new DummyData([
    'name' => 'Roman',
    'stuff' => [],
]);
```

### Flexible DTOs

```php
use romanzipp\DTO\AbstractData;

class DummyData extends AbstractData
{
    protected static bool $flexible = true;

    public string $name;
}

$data = new DummyData([
    'name' => 'Roman',
    'website' => 'ich.wtf',
]);

$data->toArray(); // ['name' => 'Roman', 'website' => 'ich.wtf];
```

### Case Formatter

```php
use romanzipp\DTO\AbstractData;
use romanzipp\DTO\Cases;

class DummyData extends AbstractData
{
    public string $firstName;
}

$data = new DummyData([
    'firstName' => 'Roman',
]);

$data->toArray();                        // ['firstName' => 'Roman'];
$data->toArray(Cases\CamelCase::class);  // ['firstName' => 'Roman'];
$data->toArray(Cases\KebabCase::class);  // ['first-name' => 'Roman'];
$data->toArray(Cases\PascalCase::class); // ['FirstName' => 'Roman'];
$data->toArray(Cases\SnakeCase::class);  // ['first_name' => 'Roman'];
```

## Validation

| Definition | Required | Value | Valid | `isset()` |
| --- | :---: | --- | :---: | :---: |
| `public $foo` | no | `''` | ✅ | ✅ |
| `public $foo` | no | `NULL` | ✅ | ✅ |
| `public $foo` | no | *none* | ✅ | ✅ |
| `public $foo` | **yes** | `''` | ✅ | ✅ |
| `public $foo` | **yes** | `NULL` | ✅ | ✅ |
| `public $foo` | **yes** | *none* | 🚫 | - |
| | | | |
| `public string $foo` | no | `''` | ✅ | ✅ |
| `public string $foo` | no | `NULL` | 🚫 | - |
| `public string $foo` | no | *none* | ✅ | 🚫 |
| `public string $foo` | **yes** | `''` | ✅ | ✅ |
| `public string $foo` | **yes** | `NULL` | 🚫 | - |
| `public string $foo` | **yes** | *none* | 🚫 | - | 
| | | | |
| `public ?string $foo` | no | `''` | ✅ | ✅ |
| `public ?string $foo` | no | `NULL` | ✅ | ✅ |
| `public ?string $foo` | no | *none* | ✅ | 🚫 |
| `public ?string $foo` | **yes** | `''` | ✅ | ✅ |
| `public ?string $foo` | **yes** | `NULL` | ✅ | ✅ |
| `public ?string $foo` | **yes** | *none* | 🚫 | - |
| | | | |
| `public ?string $foo = null` | no | `''` | ✅ | ✅ |
| `public ?string $foo = null` | no | `NULL` | ✅ | ✅ |
| `public ?string $foo = null` | no | *none* | ✅ | ✅ |
| `public ?string $foo = null` | **yes** | `''` | ⚠️* | - |
| `public ?string $foo = null` | **yes** | `NULL` | ⚠️* | - |
| `public ?string $foo = null` | **yes** | *none* | ⚠️* | - |

\* Attributes with default values cannot be required.

## Testing

```
./vendor/bin/phpunit
```

## Credits

- [Roman Zipp](https://github.com/romanzipp)

This package has been inspired by [Spaties Data-Transfer-Object](https://github.com/spatie/data-transfer-object) released under the [MIT License](https://github.com/spatie/data-transfer-object/blob/2.5.0/LICENSE.md).
