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

## Configuration

### Resolving paths

When providing paths to the `Lody::files` or `Lody::classes` methods, Lody will automatically assume these paths are within the root of your application unless they start with a slash in which case they are left untouched.

You may configure this logic by calling the `Lody::resolvePathUsing` method on one of your service providers. The example below provides the default logic.

```php
Lody::resolvePathUsing(function (string $path) {
    return Str::startsWith($path, DIRECTORY_SEPARATOR) ? $path : base_path($path);
});
```

### Resolving classnames

When using the `Lody::classes` method, Lody will transform your filenames into classnames by following the PSR-4 conventions. That is, it will start with your `App` namespace and chain the rest of the filename with backslashes instead of forward slashes. E.g. `app/Models/User` will become `App\Models\User`.

You may configure this logic by calling the `Lody::resolveClassnameUsing` method on one of your service providers. The example below provides the default logic.

```php
Lody::resolveClassnameUsing(function (SplFileInfo $file) {
    $classnameFromAppPath = str_replace(
        ['/', '.php'],
        ['\\', ''],
        Str::after($file->getRealPath(), realpath(app_path()).DIRECTORY_SEPARATOR)
    );

    return app()->getNamespace() . $classnameFromAppPath;
});
```

## References

### Lody

```php
// All return an instance of FileLazyCollection (see below).
Lody::files('app/Actions');
Lody::files(['app/Auth/Actions', 'app/Billing/Actions']);
Lody::files('app/Actions', recursive: false); // Non-recursively.
Lody::files('app/Actions', hidden: true); // Includes dot files.
Lody::filesFromFinder(Finder::create()->files()->in(app_path('Actions'))->depth(1)); // With custom finder.

// All return an instance of ClassnameLazyCollection (see below).
Lody::classes('app/Actions');
Lody::classes(['app/Auth/Actions', 'app/Billing/Actions']);
Lody::classes('app/Actions', recursive: false); // Non-recursively.
Lody::classesFromFinder(Finder::create()->files()->in(app_path('Actions'))->depth(1)); // With custom finder.

// Registering custom resolvers.
Lody::resolvePathUsing(fn(string $path) => ...);
Lody::resolveClassnameUsing(fn(SplFileInfo $file) => ...);
```

### FileLazyCollection

TODO

### ClassnameLazyCollection

TODO
