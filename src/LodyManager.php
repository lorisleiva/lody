<?php

namespace Lorisleiva\Lody;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class LodyManager
{
    protected ?Closure $pathResolver = null;
    protected ?Closure $classnameResolver = null;

    public function classes(array | string $paths, bool $recursive = true, bool $hidden = false): ClassnameLazyCollection
    {
        return $this->files($paths, $recursive, $hidden)
            ->getClassnames()
            ->classExists();
    }

    public function classesFromFinder(Finder $finder): ClassnameLazyCollection
    {
        return $this->filesFromFinder($finder)
            ->getClassnames()
            ->classExists();
    }

    public function files(array | string $paths, bool $recursive = true, bool $hidden = false): FileLazyCollection
    {
        $finder = Finder::create()
            ->in($this->resolvePaths($paths))
            ->ignoreDotFiles(! $hidden)
            ->sortByName()
            ->files();

        if (! $recursive) {
            $finder->depth(0);
        }

        return $this->filesFromFinder($finder);
    }

    public function filesFromFinder(Finder $finder): FileLazyCollection
    {
        return FileLazyCollection::make(function () use ($finder) {
            foreach ($finder as $file) {
                yield $file;
            }
        });
    }

    public function resolvePathUsing(Closure $callback): static
    {
        $this->pathResolver = $callback;

        return $this;
    }

    public function resolvePath(string $path): string
    {
        if ($resolver = $this->pathResolver) {
            return $resolver($path);
        }

        return Str::startsWith($path, DIRECTORY_SEPARATOR) ? $path : base_path($path);
    }

    public function resolveClassnameUsing(Closure $callback): static
    {
        $this->classnameResolver = $callback;

        return $this;
    }

    public function resolveClassname(SplFileInfo $file): string
    {
        if ($resolver = $this->classnameResolver) {
            return $resolver($file);
        }

        $classnameFromAppPath = str_replace(
            ['/', '.php'],
            ['\\', ''],
            Str::after($file->getRealPath(), realpath(app_path()).DIRECTORY_SEPARATOR)
        );

        return app()->getNamespace() . $classnameFromAppPath;
    }

    protected function resolvePaths(array | string $paths): array
    {
        return Collection::wrap($paths)
            ->map(fn (string $path) => $this->resolvePath($path))
            ->unique()
            ->filter(fn (string $path) => is_dir($path))
            ->values()
            ->toArray();
    }
}
