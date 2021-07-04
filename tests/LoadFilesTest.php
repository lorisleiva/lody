<?php

namespace Lorisleiva\Lody\Tests;

use Lorisleiva\Lody\FileLazyCollection;
use Lorisleiva\Lody\Lody;
use Symfony\Component\Finder\Finder;

it('loads all files from a given path', function () {
    // When we load all files in the "Stubs" directory
    // with the default options.
    $files = Lody::files(__DIR__ . '/Stubs');

    // Then we got all files recursively without hidden files.
    expectFilenames($files)->toBe([
        'DummyAbstractClass.php',
        'DummyClass.php',
        'DummyFile.php',
        'NestedDummyClass.php',
        'DummyTrait.php',
        'DummyInterface.php',
    ]);
});

it('can load hidden files as well', function () {
    // When we load all files in the "Stubs" directory
    // with the hidden option activated.
    $files = Lody::files(__DIR__ . '/Stubs', hidden: true);

    // Then we also get dot files.
    expectFilenames($files)->toContain('.DummyDotFile');
});

it('can load non-recursively as well', function () {
    // When we load all files in the "Stubs" directory
    // with the recursive option deactivated.
    $files = Lody::files(__DIR__ . '/Stubs', recursive: false);

    // Then we also get dot files.
    expectFilenames($files)->not()->toContain('NestedDummyClass.php');
});

it('can load files using a Finder instance', function () {
    // Given a Finder instance configured as we wish.
    $finder = Finder::create()
        ->in(__DIR__ . '/Stubs')
        ->files()
        ->depth(1)
        ->ignoreDotFiles(true);

    // When we fetch the files using Lody.
    $files = Lody::filesFromFinder($finder);

    // Then we get a lazy collection with the expected result.
    expect($files)->toBeInstanceOf(FileLazyCollection::class);
    expectFilenames($files)->toBe(['NestedDummyClass.php']);
});

it('can load files in multiple paths', function () {
    // Given
    // When
    // Then
});

it('uses the app namespace to resolve paths by default', function () {
    // Given
    // When
    // Then
});

it('uses a custom path resolver when provided', function () {
    // Given
    // When
    // Then
});
