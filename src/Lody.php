<?php

namespace Lorisleiva\Lody;

use Closure;
use Illuminate\Support\Facades\Facade;
use Symfony\Component\Finder\Finder;

/**
 * @method static ClassnameLazyCollection classes(array|string $paths, bool $recursive = true)
 * @method static ClassnameLazyCollection classesFromFinder(Finder $finder)
 * @method static FileLazyCollection files(array|string $paths, bool $recursive = true, bool $hidden = false)
 * @method static FileLazyCollection filesFromFinder(Finder $finder)
 * @method static LodyManager resolvePathUsing(Closure $callback)
 * @method static LodyManager resolveClassnameUsing(Closure $callback)
 * @method static LodyManager setBasePath(string $basePath)
 * @method static LodyManager setAutoloadPath(string $autoloadPath)
 *
 * @see LodyManager
 */
class Lody extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LodyManager::class;
    }
}
