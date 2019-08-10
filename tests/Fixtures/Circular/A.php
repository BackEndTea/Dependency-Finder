<?php

declare(strict_types=1);

namespace Depend\Test\Fixtures\Circular;

class A
{
    public function test(B $b) : void
    {
    }
}
