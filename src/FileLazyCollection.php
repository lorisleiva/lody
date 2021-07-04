<?php

namespace Lorisleiva\Lody;

use Illuminate\Support\LazyCollection;
use SplFileInfo;

class FileLazyCollection extends LazyCollection
{
    public function getClassnames(): ClassnameLazyCollection
    {
        $source = $this->map(
            fn (SplFileInfo $file) => Lody::resolveClassname($file)
        );

        return ClassnameLazyCollection::make($source);
    }
}
