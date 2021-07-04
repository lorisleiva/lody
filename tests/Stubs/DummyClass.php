<?php

namespace Lorisleiva\Lody\Tests\Stubs;

class DummyClass extends DummyAbstractClass implements DummyInterface
{
    use DummyTrait;

    public static function dummyStaticMethod(): void
    {
        //
    }

    public function dummyMethod(): void
    {
        //
    }
}
