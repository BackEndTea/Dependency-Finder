<?php

namespace Depend\Test\PHPParser\Visitor;

use Depend\PHPParser\Visitor\DeclarationCollector;
use Depend\PHPParser\Visitor\NameCollector;
use Depend\PHPParser\Visitor\ParentConnectorVisitor;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;

class DeclarationCollectorTest extends TestCase
{

    public function testItFindsAllDeclaredNames(): void
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

interface B{}
PHP
        );
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(new ParentConnectorVisitor());
        $declareCollector = new DeclarationCollector();
        $traverser->addVisitor($declareCollector);
        $traverser->traverse($nodes);

        $this->assertCount(2, $declareCollector->declared);
        $this->assertSame([
            'F\Foo',
            'F\B',
        ], $declareCollector->declared);
    }

}
