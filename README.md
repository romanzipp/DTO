# DTO

[![Latest Stable Version](https://img.shields.io/packagist/v/romanzipp/DTO.svg?style=flat-square)](https://packagist.org/packages/romanzipp/dto)
[![Total Downloads](https://img.shields.io/packagist/dt/romanzipp/DTO.svg?style=flat-square)](https://packagist.org/packages/romanzipp/dto)
[![License](https://img.shields.io/packagist/l/romanzipp/DTO.svg?style=flat-square)](https://packagist.org/packages/romanzipp/dto)
[![GitHub Build Status](https://img.shields.io/github/workflow/status/romanzipp/DTO/Tests?style=flat-square)](https://github.com/romanzipp/DTO/actions)

A strongly typed **Data Transfer Object** without magic for PHP 8.0+ . Features support for PHP 8 [union types](https://wiki.php.net/rfc/union_types_v2) and [attributes](https://wiki.php.net/rfc/attributes_v2).

## Contents

- [Installation](#installation)
- [Usage](#usage)
- [Validation table](#validation)

## Installation

```
composer require romanzipp/dto
```

- For **PHP 7.4** please use [`1.x`](https://github.com/romanzipp/DTO/tree/1.x)
- For **PHP 8.0** please use [`2.x`](https://github.com/romanzipp/DTO)

## Usage

```php
use romanzipp\DTO\AbstractData;
use romanzipp\DTO\Attributes\Required;

class DummyData extends AbstractData
{
    #[Required]
    public string $name;

    public ?string $nickname;

    public string|int $height;

    public DateTime $birthday;

    public bool $subscribeNewsletter = false;
}

$data = new DummyData([
    'name' => 'Roman',
    'height' => 180,
]);
```

### Require properties

When declaring required properties, the DTO will validate all parameters against the declared properties. Take a look at the [validation table](#validation) for more details.

```php
use romanzipp\DTO\AbstractData;
use romanzipp\DTO\Attributes\Required;

class DummyData extends AbstractData
{
    #[Required]
    public string $name;
}

$data = new DummyData([]);
```

> romanzipp\DTO\Exceptions\InvalidDataException: The required property \`name\` is missing

### Array methods

#### Simple array representation

To get an array representation of the DTO, simply call the `toArray` instance method.

When transferring the DTO properties to an array format, the package will respect and call any `toArray` methods of nested DTO instances or otherwise fall back to any declared [`jsonSerialize`](https://www.php.net/manual/de/jsonserializable.jsonserialize.php) method when implementing the [`JsonSerializable`](https://www.php.net/manual/de/class.jsonserializable.php) interface.

```php
use romanzipp\DTO\AbstractData;

class DummyData extends AbstractData
{
    public string $firstName;

    public DummyData $childData;
    
    /** @var self[] */
    public array $children = [];
}

$data = new DummyData([
    'firstName' => 'Roman',
    'childData' => new DummyData([
        'firstName' => 'Tim',
     ]),
    'children' => [
        new DummyData([
            'firstName' => 'Tom'
        ]),
    ],
]);

$data->toArray();
// [
//    'firstName' => 'Roman',
//    'childData' => ['firstName' => 'Tim']
//    'children' => [
//        ['firstName' => 'Tom']
//    ] 
// ];
```

#### Convert keys

The `toArrayConverted` method allows the simple conversion of property keys to a given case.

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

$data->toArrayConverted(Cases\CamelCase::class);  // ['firstName' => 'Roman'];
$data->toArrayConverted(Cases\KebabCase::class);  // ['first-name' => 'Roman'];
$data->toArrayConverted(Cases\PascalCase::class); // ['FirstName' => 'Roman'];
$data->toArrayConverted(Cases\SnakeCase::class);  // ['first_name' => 'Roman'];
```

### Flexible DTOs

When attaching the `Flexible` attribute you can provide more parameters than declared in the DTO instance.
All properties will also be included in the `toArray` methods. This would otherwise throw an [`InvalidDataException`](src/Exceptions/InvalidDataException.php).

```php
use romanzipp\DTO\AbstractData;
use romanzipp\DTO\Attributes\Flexible;

#[Flexible]
class DummyData extends AbstractData
{
    public string $name;
}

$data = new DummyData([
    'name' => 'Roman',
    'website' => 'ich.wtf',
]);

$data->toArray(); // ['name' => 'Roman', 'website' => 'ich.wtf];
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
