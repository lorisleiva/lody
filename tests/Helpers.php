<?php

namespace Lorisleiva\Lody\Tests;

use Lorisleiva\Lody\FileLazyCollection;
use Pest\Expectations\Expectation;
use SplFileInfo;

function expectFilenames(FileLazyCollection $files): Expectation
{
    $filenames = $files
        ->map(fn (SplFileInfo $file) => $file->getFilename())
        ->all();

    return expect($filenames);
}
