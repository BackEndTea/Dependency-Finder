<?php

declare(strict_types=1);

namespace Depend\Test\Fixtures\Circular;

class B
{
    public function test(A $a) : void
    {
    }
}
