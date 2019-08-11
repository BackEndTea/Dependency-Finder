<?php

declare(strict_types=1);

namespace Depend;

use Depend\Dependency\DependencyResolver;
use Depend\PHPParser\Visitor\DeclarationCollector;
use Depend\PHPParser\Visitor\NameCollector;
use Depend\PHPParser\Visitor\ParentConnectorVisitor;
use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use function array_unique;
use function file_get_contents;

class DependencyFinder
{
    /** @var array<string> */
    private $directories;
    /** @var array<string> */
    private $exclude;

    /**
     * Key: File name
     * Value: Classes/functions that this file depends on.
     *
     * @var array<string, array<string>> $dependencyMap
     */
    private $dependencyMap = [];

    /**
     * Key: Class/ function name
     * Value: Files that have a dependency on the class.
     *
     * @var array<string, array<string>> $dependantMap
     */
    private $dependantMap = [];

    /**
     * Key: File name
     * Value: Classes it declares
     *
     * @var array<string, array<string>> $declareMap
     */
    private $declareMap = [];

    /** @var Parser  */
    private $parser;

    /**
     * List of directories to build dependencies from.
     * for example: `new DependencyFinder(['./src', './tests', './lib']);`
     *
     * @param array<string> $directories
     * @param array<string> $exclude
     */
    public function __construct(array $directories, array $exclude = [])
    {
        $this->directories = $directories;
        $this->exclude     = $exclude;
        $this->parser      = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
    }

    public function build() : void
    {
        /** @var SplFileInfo $file */
        foreach (Finder::create()
            ->in($this->directories)
            ->exclude($this->exclude)
            ->files() as $file) {
            $path = $file->getRealPath();
            if ($path === false) {
                $path = '';
            }

            $this->reBuildFor($path);
        }
    }

    /**
     * @param array<string> $fileNames
     *
     * @return array<string>
     */
    public function getAllFilesDependingOn(array $fileNames) : array
    {
        return(new DependencyResolver())->resolve($fileNames, $this->declareMap, $this->dependantMap);
    }

    /**
     * @param array<string> $fileNames
     */
    public function reBuild(array $fileNames) : void
    {
        foreach ($fileNames as $fileName) {
            $this->reBuildFor($fileName);
        }
    }

    private function reBuildFor(string $fileName) : void
    {
        $info                           = $this->traversePath($fileName);
        $this->dependencyMap[$fileName] = array_unique($info->dependencies);
        $this->declareMap[$fileName]    = $info->declares;
        foreach ($info->dependencies as $dependency) {
            $this->dependantMap[$dependency][] = $fileName;
        }
    }

    private function traversePath(string $filePath) : File
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return new File();
        }

        try {
            $nodes = $this->parser->parse($content);
        } catch (Error $e) {
            return new File();
        }
        assert(is_array($nodes));
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(new ParentConnectorVisitor());
        $nameCollector    = new NameCollector();
        $declareCollector = new DeclarationCollector();
        $traverser->addVisitor($nameCollector);
        $traverser->addVisitor($declareCollector);
        $traverser->traverse($nodes);

        return new File($declareCollector->declared, $nameCollector->resolvedNames);
    }
}
