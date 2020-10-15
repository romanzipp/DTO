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

## Validation

| Definition | Required | Value | Valid | `isset()` |
| --- | :---: | --- | :---: | --- |
| `public $foo` | no | `''` | ✅ | **true** |
| `public $foo` | no | `NULL` | ✅ | **true** |
| `public $foo` | no | *none* | ✅ | **true** |
| `public $foo` | **yes** | `''` | ✅ | **true** |
| `public $foo` | **yes** | `NULL` | ✅ | **true** |
| `public $foo` | **yes** | *none* | 🚫 | - |
| | | | |
| `public string $foo` | no | `''` | ✅ | **true** |
| `public string $foo` | no | `NULL` | 🚫 | - |
| `public string $foo` | no | *none* | ✅ | false |
| `public string $foo` | **yes** | `''` | ✅ | **true** |
| `public string $foo` | **yes** | `NULL` | 🚫 | - |
| `public string $foo` | **yes** | *none* | 🚫 | - | 
| | | | |
| `public ?string $foo` | no | `''` | ✅ | **true** |
| `public ?string $foo` | no | `NULL` | ✅ | **true** |
| `public ?string $foo` | no | *none* | ✅ | false |
| `public ?string $foo` | **yes** | `''` | ✅ | **true** |
| `public ?string $foo` | **yes** | `NULL` | ✅ | **true** |
| `public ?string $foo` | **yes** | *none* | 🚫 | - |
| | | | |
| `public ?string $foo = null` | no | `''` | ✅ | **true** |
| `public ?string $foo = null` | no | `NULL` | ✅ | **true** |
| `public ?string $foo = null` | no | *none* | ✅ | **true** |
| `public ?string $foo = null` | **yes** | `''` | ⚠️* | - |
| `public ?string $foo = null` | **yes** | `NULL` | ⚠️* | - |
| `public ?string $foo = null` | **yes** | *none* | ⚠️* | - |

\* Attributes with default values cannot be required.

## Testing

```
./vendor/bin/phpunit
```
