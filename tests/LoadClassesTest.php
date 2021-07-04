<?php

namespace Lorisleiva\Lody\Tests;

use Illuminate\Support\Str;
use Lorisleiva\Lody\Lody;
use Lorisleiva\Lody\Tests\Stubs\DummyAbstractClass;
use Lorisleiva\Lody\Tests\Stubs\DummyClass;
use Lorisleiva\Lody\Tests\Stubs\NestedStubs\NestedDummyClass;
use Lorisleiva\Lody\Tests\Stubs\SecondNestedStubs\SecondNestedDummyClass;
use SplFileInfo;

beforeEach(function () {
    Lody::resolveClassnameUsing(function (SplFileInfo $file): string {
        return Str::of($file->getRealPath())
            ->after(realpath(__DIR__).DIRECTORY_SEPARATOR)
            ->beforeLast('.php')
            ->replace('/', '\\')
            ->prepend('Lorisleiva\\Lody\\Tests\\');
    });
});

it('loads all existing classes from a given path', function () {
    // When we load all classes in the "Stubs" directory
    // with the default options.
    $files = Lody::classes(__DIR__ . '/Stubs');

    // Then we got all existing classes recursively.
    expect($files->all())->toBe([
        DummyAbstractClass::class,
        DummyClass::class,
        NestedDummyClass::class,
        SecondNestedDummyClass::class,
    ]);
});

it('can load classes non-recursively as well', function () {
    // When we load all classes in the "Stubs" directory
    // with the recursive option deactivated.
    $files = Lody::classes(__DIR__ . '/Stubs', recursive: false);

    // Then we do not get the nested classes.
    expect($files)->not()->toContain(NestedDummyClass::class);
    expect($files)->not()->toContain(SecondNestedDummyClass::class);
});
