<?php

namespace Lorisleiva\Lody\Tests;

use Lorisleiva\Lody\FileLazyCollection;
use SplFileInfo;

function expectFilenames(FileLazyCollection $files)
{
    $filenames = $files
        ->map(fn (SplFileInfo $file) => $file->getFilename())
        ->all();

    return expect($filenames);
}
