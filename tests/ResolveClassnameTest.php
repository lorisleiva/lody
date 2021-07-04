<?php

namespace Lorisleiva\Lody\Tests;

use Illuminate\Support\Str;
use Lorisleiva\Lody\Lody;
use SplFileInfo;

it('transforms a file into a classname using PSR-4 by default', function () {
    // Given a file and no custom classname resolver is provided.
    // -- Note that Testbench Orchestra does not have any class on their
    // -- test laravel application folder which is why we're using "Models" here
    // -- instead of "Models/User" for example. But the transformation is the same.
    $file = new SplFileInfo(base_path('app/Models'));

    // When we resolve the classname of that file.
    $resolvedPath = Lody::resolveClassname($file);

    // Then we get the classname using the PSR-4 convention.
    expect($resolvedPath)->toBe('App\Models');
});

it('uses a custom classname resolver when provided', function () {
    // Given we provide a custom classname resolver
    // that reverses slashes and all segment of a path.
    Lody::resolveClassnameUsing(function (SplFileInfo $file) {
        return Str::of($file->getRealPath())
            ->after(realpath(__DIR__).DIRECTORY_SEPARATOR)
            ->beforeLast('.php')
            ->explode('/')
            ->reverse()
            ->join('\\');
    });

    // And given the following file.
    $file = new SplFileInfo(__DIR__ . '/Stubs/DummyClass.php');

    // When we resolve the classname for that file.
    $resolvedClassname = Lody::resolveClassname($file);

    // Then the custom resolver logic was used.
    expect($resolvedClassname)->toBe('DummyClass\Stubs');
});
