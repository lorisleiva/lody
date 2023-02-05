# 🗄 Lody

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lorisleiva/lody.svg)](https://packagist.org/packages/lorisleiva/lody)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/lorisleiva/lody/run-tests.yml?branch=main)](https://github.com/lorisleiva/lody/actions?query=workflow%3ATests+branch%3Amain)
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

When using the `Lody::classes` method, Lody will transform your filenames into classnames **by following PSR-4 conventions**. For example, if your filename is `app/Models/User.php` and you have mapped the `App` namespace to the `app` directory in your `composer.json` file, the it will be resolved to `App\Models\User`.

By default, the classname resolution takes into account every single PSR-4 mapping as defined in your `vendor/composer/autoload_psr4.php` file. This means, it will even resolves classes that live in your vendor directory properly.

If your PSR-4 autoload file is located elsewhere, you may configure it by calling the `Lody::setAutoloadPath` method on one of your service providers.

```php
Lody::setAutoloadPath('my/custom/autoload_psr4.php');
```

Alternatively, you may override this logic entirely by calling the `Lody::resolveClassnameUsing` method. The example below provides a useful example for Laravel applications.

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

### Using Lody without Laravel

Lody works out of the box with Laravel because we can use the `base_path` method to access the root of your project.

However, if you wish to use Lody without Laravel, you may simply provide the base path of your application explicitely using the `Lody::setBasePath` method.

```php
// Assuming this is executed at the root of your project.
Lody::setBasePath(__DIR__);
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

```php
// Transforms files into classnames and returns a `ClassnameLazyCollection`.
// Note that these can still be invalid classes. See `classExists` below.
Lody::files('...')->getClassnames();
```

### ClassnameLazyCollection

```php
// The `classExists` rejects all classnames that do not reference a valid PHP class.
Lody::files('...')->getClassnames()->classExists();

// Note that this is equivalent to the line above.
Lody::classes('...');

// Filter abstract classes.
Lody::classes('...')->isAbstract();
Lody::classes('...')->isNotAbstract();

// Filter classes based on inheritance.
Lody::classes('...')->isInstanceOf(SomeClassOrInterface::class);
Lody::classes('...')->isNotInstanceOf(SomeClassOrInterface::class);

// Filter classes based on traits.
Lody::classes('...')->hasTrait(SomeTrait::class);
Lody::classes('...')->hasTrait(SomeTrait::class, recursive: false); // Don't include recursive traits.
Lody::classes('...')->doesNotHaveTrait(SomeTrait::class);
Lody::classes('...')->doesNotHaveTrait(SomeTrait::class, recursive: false); // Don't include recursive traits.

// Filter classes based on method it contains or not.
Lody::classes('...')->hasMethod('someMethod');
Lody::classes('...')->hasStaticMethod('someMethod'); // Ensures the method is static.
Lody::classes('...')->hasNonStaticMethod('someMethod'); // Ensures the method is non-static.
Lody::classes('...')->doesNotHaveMethod('someMethod');
```
