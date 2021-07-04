<?php

namespace Lorisleiva\Lody\Tests;

use Illuminate\Support\Str;
use Lorisleiva\Lody\Lody;
use SplFileInfo;

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
