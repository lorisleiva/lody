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
        'DummyInterface.php',
        'DummyTrait.php',
        'NestedDummyClass.php',
        'SecondNestedDummyClass.php',
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
        ->sortByName()
        ->in(__DIR__ . '/Stubs')
        ->files()
        ->depth(1)
        ->ignoreDotFiles(true);

    // When we fetch the files using Lody.
    $files = Lody::filesFromFinder($finder);

    // Then we get a lazy collection with the expected result.
    expect($files)->toBeInstanceOf(FileLazyCollection::class);
    expectFilenames($files)->toBe([
        'NestedDummyClass.php',
        'SecondNestedDummyClass.php',
    ]);
});

it('can load files in multiple paths', function () {
    // When we load files in multiple paths.
    $files = Lody::files([
        __DIR__ . '/Stubs/NestedStubs',
        __DIR__ . '/Stubs/SecondNestedStubs',
    ]);

    // Then we can files within each of these paths.
    expectFilenames($files)->toBe([
        'NestedDummyClass.php',
        'SecondNestedDummyClass.php',
    ]);
});

it('returns the same file only once', function () {
    // When we load the same folder more than once.
    $files = Lody::files([
        __DIR__ . '/Stubs/NestedStubs',
        __DIR__ . '/Stubs/NestedStubs',
    ]);

    // Then its content only appears once.
    expectFilenames($files)->toBe([
        'NestedDummyClass.php',
    ]);
});
