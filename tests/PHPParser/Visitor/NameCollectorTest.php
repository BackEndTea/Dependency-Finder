<?php

declare(strict_types=1);

namespace Depend\Test\PHPParser\Visitor;

use Depend\PHPParser\Visitor\NameCollector;
use Depend\PHPParser\Visitor\ParentConnectorVisitor;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;

class NameCollectorTest extends TestCase
{
    public function testItFindsAllNames() : void
    {
        $parser    = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $nodes     = $parser->parse(<<<'PHP'
<?php
namespace F;


class Foo 
{
    public function __construct(Bar\Baz $a) {}
    public function hello(\Full\Name $b): string 
    {
        return false;
    }
}
PHP
        );
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(new ParentConnectorVisitor());
        $nameCollector = new NameCollector();
        $traverser->addVisitor($nameCollector);
        $traverser->traverse($nodes);

        $this->assertCount(2, $nameCollector->resolvedNames);
        $this->assertSame([
            '\F\Bar\Baz',
            '\Full\Name',
        ], $nameCollector->resolvedNames);
    }
}
