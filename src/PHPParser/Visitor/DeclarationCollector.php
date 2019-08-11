<?php

declare(strict_types=1);

namespace Depend\PHPParser\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * @internal
 */
final class DeclarationCollector extends NodeVisitorAbstract
{
    /** @var array<string>  */
    public $declared = [];

    /**
     * @param array<Node|mixed> $nodes
     *
     * @return array<Node>
     */
    public function beforeTraverse(array $nodes) : ?array
    {
        $this->declared = [];

        return null;
    }

    public function enterNode(Node $node) : ?Node
    {
        /** @var Node\Name|null $name */
        $name = $node->namespacedName ?? null;
        if ($name instanceof Node\Name) {
            $this->declared[] = $name->toCodeString();
        }

        return null;
    }
}
