<?php

declare(strict_types=1);

namespace Depend\Test;

use Depend\DependencyFinder;
use PHPUnit\Framework\TestCase;

class DependencyFinderTest extends TestCase
{
    public function testItCanDealWithCircularDependencies() : void
    {
        $finder = new DependencyFinder([__DIR__ . '/Fixtures/Circular']);

        $finder->build();
        $deps = $finder->getAllFilesDependingOn(__DIR__ . '/Fixtures/Circular/A.php');

        $this->assertCount(2, $deps);
    }

    public function testAWrongFileReturnsNoDependencies() : void
    {
        $finder = new DependencyFinder([__DIR__ . '/Fixtures/Circular']);
        $finder->build();

        $deps = $finder->getAllFilesDependingOn(__FILE__ . 'asdf');
        $this->assertCount(0, $deps);
    }
}
