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
    protected ?string $basePath = null;
    protected ?string $autoloadPath = null;

    public function classes(array | string $paths, bool $recursive = true): ClassnameLazyCollection
    {
        return $this->files($paths, $recursive)
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

        $startsWithDirectorySeparator = Str::startsWith($path, ['/', '\\']);
        $startsWithWindowsDisk = (bool) preg_match('~\A[A-Z]:(?![^/\\\\])~i', $path);

        if ($startsWithDirectorySeparator || $startsWithWindowsDisk) {
            return $path;
        }

        return $this->getBasePath($path);
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

        /** @var Psr4Resolver $psr4Resolver */
        $psr4Resolver = app(Psr4Resolver::class);

        return $psr4Resolver->resolve($file->getRealPath());
    }

    public function setBasePath(string $basePath): static
    {
        $this->basePath = $basePath;

        return $this;
    }

    public function getBasePath(string $path = ''): string
    {
        $basePath = $this->basePath ?? base_path();

        return $basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    public function setAutoloadPath(string $autoloadPath): static
    {
        $this->autoloadPath = $autoloadPath;

        return $this;
    }

    public function getAutoloadPath(): string
    {
        return $this->autoloadPath
            ?? $this->getBasePath('vendor/composer/autoload_psr4.php');
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
