<?php

namespace Lorisleiva\Lody\Tests;

use Lorisleiva\Lody\Lody;

it('loads all files from a given path', function () {
    // When we load all files in the "Stubs" directory with default options.
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
