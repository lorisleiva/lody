<?php

namespace Lorisleiva\Lody;

use Illuminate\Support\LazyCollection;
use ReflectionClass;
use ReflectionMethod;

class ClassnameLazyCollection extends LazyCollection
{
    public function classExists(): static
    {
        return $this->filter(
            fn (string $classname) => class_exists($classname)
        )->values();
    }

    public function isAbstract(bool $expected = true): static
    {
        return $this->filter(
            fn (string $classname) => (new ReflectionClass($classname))->isAbstract() === $expected
        )->values();
    }

    public function isNotAbstract(): static
    {
        return $this->isAbstract(expected: false);
    }

    public function isInstanceOf(string $superclass, bool $expected = true): static
    {
        return $this->filter(
            fn (string $classname) => is_subclass_of($classname, $superclass) === $expected
        )->values();
    }

    public function isNotInstanceOf(string $superclass): static
    {
        return $this->isInstanceOf($superclass, expected: false);
    }

    public function hasTrait(string $trait, bool $recursive = true, bool $expected = true): static
    {
        return $this->filter(function (string $classname) use ($trait, $recursive, $expected) {
            $usedTraits = $recursive ? class_uses_recursive($classname) : class_uses($classname);

            return in_array($trait, $usedTraits) === $expected;
        })->values();
    }

    public function doesNotHaveTrait($trait, bool $recursive = true): static
    {
        return $this->hasTrait($trait, $recursive, expected: false);
    }

    public function hasMethod(string $method, ?bool $static = null, bool $expected = true): static
    {
        return $this->filter(function (string $classname) use ($method, $static, $expected) {
            if (! method_exists($classname, $method)) {
                return ! $expected;
            }

            $staticConstraint = is_null($static) || (new ReflectionMethod($classname, $method))->isStatic() === $static;

            return $expected && $staticConstraint;
        })->values();
    }

    public function hasStaticMethod($method): static
    {
        return $this->hasMethod($method, static: true);
    }

    public function hasNonStaticMethod($method): static
    {
        return $this->hasMethod($method, static: false);
    }

    public function doesNotHaveMethod(string $method): static
    {
        return $this->hasMethod($method, expected: false);
    }
}
