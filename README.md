# DTO

[![Latest Stable Version](https://img.shields.io/packagist/v/romanzipp/DTO.svg?style=flat-square)](https://packagist.org/packages/romanzipp/dto)
[![Total Downloads](https://img.shields.io/packagist/dt/romanzipp/DTO.svg?style=flat-square)](https://packagist.org/packages/romanzipp/dto)
[![License](https://img.shields.io/packagist/l/romanzipp/DTO.svg?style=flat-square)](https://packagist.org/packages/romanzipp/dto)
[![Code Quality](https://img.shields.io/scrutinizer/g/romanzipp/DTO.svg?style=flat-square)](https://scrutinizer-ci.com/g/romanzipp/DTO/?branch=master)
[![GitHub Build Status](https://img.shields.io/github/workflow/status/romanzipp/DTO/Tests?style=flat-square)](https://github.com/romanzipp/DTO/actions)

A strongly typed Data Transfer Object without magic for PHP 7.4+

## Installation

```
composer require romanzipp/dto
```

## Usage

```php
use romanzipp\DTO\AbstractData;

class MyData extends AbstractData
{
    protected static array $required = [
        'name',
        'stuff',
    ];

    public string $name;

    public $stuff;

    public ?string $nickname;

    public DateTime $birthday;

    public bool $subscribeNewsletter = false;
} 
```

### Available Methods

```php
use romanzipp\DTO\AbstractData;

class DummyData extends AbstractData
{
    public string $name;
}

$data = new DummyData([
    'name' => 'Roman',
]);

$data->toArray(); // ['name' => 'Roman'];
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
