<?php

namespace Lorisleiva\Lody;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Psr4Resolver
{
    protected LodyManager $lody;
    protected bool $psr4Loaded = false;
    protected array $psr4Dictionary = [];

    public function __construct(LodyManager $lody)
    {
        $this->lody = $lody;
    }

    public function resolve(string $filename): string
    {
        [$pathPrefix, $classPrefix] = $this->findPrefixes($filename);

        return Str::of($filename)
            ->after($pathPrefix)
            ->beforeLast('.php')
            ->replace('/', '\\')
            ->prepend($classPrefix);
    }

    public function findPrefixes(string $filename): array
    {
        $directory = $this->getPsr4Dictionary();
        $fragments = array_reverse(explode(DIRECTORY_SEPARATOR, $filename));
        $accumulator = $filename;

        foreach ($fragments as $fragment) {
            $accumulator = Str::beforeLast($accumulator, DIRECTORY_SEPARATOR.$fragment);

            if ($classPrefix = ($directory[$accumulator] ?? false)) {
                return [realpath($accumulator).DIRECTORY_SEPARATOR, $classPrefix];
            }
        }

        return ['', ''];
    }

    public function getPsr4Namespaces(): array
    {
        return require($this->lody->getAutoloadPath());
    }

    public function getPsr4Dictionary(): array
    {
        if (! $this->psr4Loaded) {
            $this->loadPsr4Dictionary();
        }

        return $this->psr4Dictionary;
    }

    public function loadPsr4Dictionary(): void
    {
        foreach ($this->getPsr4Namespaces() as $classPrefix => $paths) {
            $this->add($classPrefix, $paths);
        }

        $this->psr4Loaded = true;
    }

    public function add(string $classPrefix, string | array $paths): void
    {
        foreach (Arr::wrap($paths) as $path) {
            $this->psr4Dictionary[$this->unifyDirectorySeparator($path)] = $classPrefix;
        }
    }

    protected function unifyDirectorySeparator(string $path): string
    {
        if (DIRECTORY_SEPARATOR !== '\\') {
            return $path;
        }

        return str_replace('/', '\\', $path);
    }
}
