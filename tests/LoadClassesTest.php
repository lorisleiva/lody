<?php

namespace Lorisleiva\Lody\Tests;

use Lorisleiva\Lody\Lody;
use Lorisleiva\Lody\Tests\Stubs\DummyAbstractClass;
use Lorisleiva\Lody\Tests\Stubs\DummyClass;
use Lorisleiva\Lody\Tests\Stubs\DummyInterface;
use Lorisleiva\Lody\Tests\Stubs\DummyTrait;
use Lorisleiva\Lody\Tests\Stubs\NestedStubs\NestedDummyClass;
use Lorisleiva\Lody\Tests\Stubs\SecondNestedStubs\SecondNestedDummyClass;

beforeEach(function () {
    Lody::setBasePath(__DIR__ . '/..');
});

it('loads all existing classes from a given path', function () {
    // When we load all classes in the "Stubs" directory
    // with the default options.
    $files = Lody::classes('tests/Stubs')->all();

    // Then we got all existing classes recursively.
    expect($files)->toBe([
        DummyAbstractClass::class,
        DummyClass::class,
        NestedDummyClass::class,
        SecondNestedDummyClass::class,
    ]);
});

it('can load classes non-recursively as well', function () {
    // When we load all classes in the "Stubs" directory
    // with the recursive option deactivated.
    $files = Lody::classes('tests/Stubs', recursive: false)->all();

    // Then we do not get the nested classes.
    expect($files)->not()->toContain(NestedDummyClass::class);
    expect($files)->not()->toContain(SecondNestedDummyClass::class);
});

it('can filter abstract classes', function () {
    // Is abstract.
    $files = Lody::classes('tests/Stubs')->isAbstract()->all();
    expect($files)->toBe([DummyAbstractClass::class]);

    // Is not abstract.
    $files = Lody::classes('tests/Stubs')->isNotAbstract()->all();
    expect($files)->not()->toContain(DummyAbstractClass::class);
});

it('can filter classes based on inheritance', function () {
    // Is instance of.
    $files = Lody::classes('tests/Stubs')->isInstanceOf(DummyAbstractClass::class)->all();
    expect($files)->toBe([DummyClass::class]);

    // Is not instance of.
    $files = Lody::classes('tests/Stubs')->isNotInstanceOf(DummyAbstractClass::class)->all();
    expect($files)->not()->toContain(DummyClass::class);

    // Is instance of interface.
    $files = Lody::classes('tests/Stubs')->isInstanceOf(DummyInterface::class)->all();
    expect($files)->toBe([DummyClass::class]);
});

it('can filter classes based on used traits', function () {
    // Has trait.
    $files = Lody::classes('tests/Stubs')->hasTrait(DummyTrait::class)->all();
    expect($files)->toBe([DummyClass::class]);

    // Does not have trait.
    $files = Lody::classes('tests/Stubs')->doesNotHaveTrait(DummyTrait::class)->all();
    expect($files)->not()->toContain(DummyClass::class);

    // Has direct trait.
    $files = Lody::classes('tests/Stubs')->hasTrait(DummyTrait::class, recursive: false)->all();
    expect($files)->toBe([DummyClass::class]);

    // Does not have direct trait.
    $files = Lody::classes('tests/Stubs')->doesNotHaveTrait(DummyTrait::class, recursive: false)->all();
    expect($files)->not()->toContain(DummyClass::class);
});

it('can filter classes based on their methods', function () {
    // Has method.
    $files = Lody::classes('tests/Stubs')->hasMethod('dummyMethod')->all();
    expect($files)->toBe([DummyClass::class]);
    $files = Lody::classes('tests/Stubs')->hasMethod('dummyStaticMethod')->all();
    expect($files)->toBe([DummyClass::class]);

    // Does not have method.
    $files = Lody::classes('tests/Stubs')->doesNotHaveMethod('dummyMethod')->all();
    expect($files)->not()->toContain(DummyClass::class);
    $files = Lody::classes('tests/Stubs')->doesNotHaveMethod('dummyStaticMethod')->all();
    expect($files)->not()->toContain(DummyClass::class);

    // Has static method.
    $files = Lody::classes('tests/Stubs')->hasStaticMethod('dummyStaticMethod')->all();
    expect($files)->toBe([DummyClass::class]);
    $files = Lody::classes('tests/Stubs')->hasStaticMethod('dummyMethod')->all();
    expect($files)->not()->toContain(DummyClass::class);

    // Has non-static method.
    $files = Lody::classes('tests/Stubs')->hasNonStaticMethod('dummyMethod')->all();
    expect($files)->toBe([DummyClass::class]);
    $files = Lody::classes('tests/Stubs')->hasNonStaticMethod('dummyStaticMethod')->all();
    expect($files)->not()->toContain(DummyClass::class);
});
