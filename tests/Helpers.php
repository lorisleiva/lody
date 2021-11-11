<?php

namespace Lorisleiva\Lody\Tests;

use Lorisleiva\Lody\FileLazyCollection;
use Lorisleiva\Lody\Lody;
use SplFileInfo;

function expectFilenames(FileLazyCollection $files)
{
    $filenames = $files
        ->map(fn (SplFileInfo $file) => $file->getFilename())
        ->all();

    return expect($filenames);
}

function resolveClassname(string $filename): string
{
    return Lody::resolveClassname(new SplFileInfo($filename));
}
