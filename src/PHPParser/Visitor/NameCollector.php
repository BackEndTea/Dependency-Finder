<?php

declare(strict_types=1);

namespace Depend\PHPParser\Visitor;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

final class NameCollector extends NodeVisitorAbstract
{
    /** @var array<string> */
    public $resolvedNames = [];

    /**
     * @param array<Node|mixed> $nodes
     *
     * @return array<Node>|null
     */
    public function beforeTraverse(array $nodes) : ?array
    {
        $this->resolvedNames = [];

        return null;
    }

    /**
     * @return int|Node|null
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Use_) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }
        if ($node instanceof Node\Name) {
            /** @var Node|null $parent */
            $parent = $node->getAttribute(ParentConnectorVisitor::PARENT_KEY);
            if ($parent instanceof Node\Stmt\Namespace_
                || $parent instanceof Node\Expr\ConstFetch
                || $parent instanceof Node\Expr\ClassConstFetch
            ) {
                return null;
            }

            $this->resolvedNames[] = $node->toCodeString();
        }

        return null;
    }
}
