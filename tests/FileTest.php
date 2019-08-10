<?php

declare(strict_types=1);

namespace Depend\Test;

use Depend\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testItCanBeInstantiated() : void
    {
        $file = new File();
        $this->assertSame([], $file->dependencies);
        $this->assertSame([], $file->declares);
    }
}
