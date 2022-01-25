<?php

namespace Lorisleiva\Lody\Tests;

use Illuminate\Support\Str;
use Lorisleiva\Lody\Lody;
use Lorisleiva\Lody\LodyManager;
use Lorisleiva\Lody\Psr4Resolver;
use Lorisleiva\Lody\Tests\Stubs\DummyClass;
use Lorisleiva\Lody\Tests\Stubs\NestedStubs\NestedDummyClass;
use SplFileInfo;

beforeEach(function () {
    Lody::setBasePath(__DIR__ . '/..');
});

it('resolves any classname defined within PSR-4', function () {
    // Within the test namespace.
    expect(resolveClassname(__DIR__ . '/Stubs/DummyClass.php'))
        ->toBe(DummyClass::class);
    expect(resolveClassname(__DIR__ . '/Stubs/NestedStubs/NestedDummyClass.php'))
        ->toBe(NestedDummyClass::class);

    // Within the src namespace.
    expect(resolveClassname(__DIR__ . '/../src/LodyManager.php'))
        ->toBe(LodyManager::class);
    expect(resolveClassname(__DIR__ . '/../src/Psr4Resolver.php'))
        ->toBe(Psr4Resolver::class);

    // Within vendor namespaces.
    expect(resolveClassname(__DIR__ . '/../vendor/laravel/framework/src/Illuminate/Support/Str.php'))
        ->toBe(Str::class);
});

it('uses a custom classname resolver when provided', function () {
    // Given we provide a custom classname resolver
    // that reverses slashes and all segment of a path.
    Lody::resolveClassnameUsing(function (SplFileInfo $file) {
        return Str::of($file->getRealPath())
            ->after(realpath(__DIR__).DIRECTORY_SEPARATOR)
            ->beforeLast('.php')
            ->explode(DIRECTORY_SEPARATOR)
            ->reverse()
            ->join('\\');
    });

    // When we resolve the classname for a given filename.
    $resolvedClassname = resolveClassname(__DIR__ . '/Stubs/DummyClass.php');

    // Then the custom resolver logic was used.
    expect($resolvedClassname)->toBe('DummyClass\Stubs');
});
