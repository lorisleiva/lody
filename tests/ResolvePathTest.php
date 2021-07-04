<?php

namespace Lorisleiva\Lody\Tests;

use Lorisleiva\Lody\Lody;

it('uses the base path to resolve paths by default', function () {
    // Given no custom path resolver is provided.

    // When we resolve a path without an initial slash.
    $resolvedPath = Lody::resolvePath('app/Models/User');

    // Then our path is now prefixed with the app's base path.
    expect($resolvedPath)->toBe(base_path('app/Models/User'));
});

it('does not prepend the base path if the path starts with a slash', function () {
    // Given no custom path resolver is provided.

    // When we resolve a path with an initial slash.
    $resolvedPath = Lody::resolvePath('/var/www/app/Models/User');

    // Then our path is left untouched.
    expect($resolvedPath)->toBe('/var/www/app/Models/User');
});

it('uses a custom path resolver when provided', function () {
    // Given we provide a custom path resolver
    // that reverses all segment of a path.
    Lody::resolvePathUsing(function (string $path) {
        return implode('/', array_reverse(explode('/', $path)));
    });

    // When we resolve any path.
    $resolvedPath = Lody::resolvePath('app/Models/User');

    // Then that path was reversed.
    expect($resolvedPath)->toBe('User/Models/app');
});
