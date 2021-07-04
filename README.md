# 🗄 Lody

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lorisleiva/lody.svg)](https://packagist.org/packages/lorisleiva/lody)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/lorisleiva/package-lody-laravel/Tests?label=tests)](https://github.com/lorisleiva/package-lody-laravel/actions?query=workflow%3ATests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/lorisleiva/lody.svg)](https://packagist.org/packages/lorisleiva/lody)

Load files and classes as lazy collections in Laravel.

## Installation

```bash
composer require lorisleiva/lody
```

## Usage

Lody enables you to fetch all existing PHP classes of a provided path (or array of paths) that are relative to your application's base path. It returns a custom `LazyCollection` with helpful methods so that you can filter classes even further based on your own requirement. For example, the code below will fetch all non-abstract instances of `Node` within the given path recursively and register each of them.

``` php
use Lorisleiva\Lody\Lody;

Lody::classes('app/Workflow/Nodes')
    ->isNotAbstract()
    ->isInstanceOf(Node::class)
    ->each(fn (string $classname) => $this->register($classname));
```

If you want all files instead of existing PHP classes, you may use `Lody::files` instead.

``` php
use Lorisleiva\Lody\Lody;

Lody::files('app/Workflow/Nodes')
    ->each(fn (SplFileInfo $file) => $this->register($file));
```

## References

### Lody

```php
// All return an instance of FileLazyCollection (see below).
Lody::files('app/Actions');
Lody::files(['app/Auth/Actions', 'app/Billing/Actions']);
Lody::files('app/Actions', recursive: false); // Non-recursively.
Lody::files('app/Actions', hidden: true); // Includes dot files.
Lody::filesFromFinder(Finder::create()->files()->in('app/Actions')->depth(1)); // With custom finder.

// All return an instance of ClassnameLazyCollection (see below).
Lody::classes('app/Actions');
Lody::classes(['app/Auth/Actions', 'app/Billing/Actions']);
Lody::classes('app/Actions', recursive: false); // Non-recursively.
Lody::classesFromFinder(Finder::create()->files()->in('app/Actions')->depth(1)); // With custom finder.
```
