# DTO

[![Latest Stable Version](https://img.shields.io/packagist/v/romanzipp/DTO.svg?style=flat-square)](https://packagist.org/packages/romanzipp/dto)
[![Total Downloads](https://img.shields.io/packagist/dt/romanzipp/DTO.svg?style=flat-square)](https://packagist.org/packages/romanzipp/dto)
[![License](https://img.shields.io/packagist/l/romanzipp/DTO.svg?style=flat-square)](https://packagist.org/packages/romanzipp/dto)
[![Code Quality](https://img.shields.io/scrutinizer/g/romanzipp/DTO.svg?style=flat-square)](https://scrutinizer-ci.com/g/romanzipp/DTO/?branch=master)
[![GitHub Build Status](https://img.shields.io/github/workflow/status/romanzipp/DTO/Tests?style=flat-square)](https://github.com/romanzipp/DTO/actions)

## Installation

```
composer require romanzipp/dto
```

## Usage

## Validation

| Definition | Required | Value | Valid |
| --- | :---: | --- | :---: |
| `public $foo` | no | `''` | ✅ |
| `public $foo` | no | `NULL` | ✅ |
| `public $foo` | no | *none* | ✅ |
| | | |
| `public string $foo` | no | `''` | ✅ |
| `public string $foo` | no | `NULL` | 🚫 |
| `public string $foo` | no | *none* | 🚫 |
| | | |
| `public string $foo` | yes | `''` | ✅ |
| `public string $foo` | yes | `NULL` | 🚫 |
| `public string $foo` | yes | *none* | 🚫 | 
| | | |
| `public ?string $foo` | no | `''` | ✅ |
| `public ?string $foo` | no | `NULL` | ✅ |
| `public ?string $foo` | no | *none* | 🚫 |

## Testing

```
./vendor/bin/phpunit
```
