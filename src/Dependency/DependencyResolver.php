<?php

declare(strict_types=1);

namespace Depend\Dependency;

use function array_key_exists;
use function array_push;
use function realpath;

class DependencyResolver
{
    /** @var array<string, true> */
    private $resolved = [];

    /** @var array<string> */
    private $knownDependencies = [];

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

    /**
     * @param array<string>                $fileNames
     * @param array<string, array<string>> $declareMap
     * @param array<string, array<string>> $dependantMap
     *
     * @return array<string>
     */
    public function resolve(array $fileNames, array $declareMap, array $dependantMap) : array
    {
        $this->declareMap   = $declareMap;
        $this->dependantMap = $dependantMap;
        foreach ($fileNames as $fileName) {
            $fileName = realpath($fileName);
            foreach ($this->declareMap[$fileName]?? [] as $class) {
                $this->resolveClass($class);
            }
        }

        return $this->knownDependencies;
    }

    private function resolveClass(string $className) : void
    {
        if (array_key_exists($className, $this->resolved)) {
            return;
        }
        $this->resolved[$className] = true;

        $dependants = $this->dependantMap['\\' . $className] ?? [];

        array_push($this->knownDependencies, ...$dependants);
        foreach ($dependants as $dependant) {
            foreach ($this->declareMap[$dependant]?? [] as $class) {
                $this->resolveClass($class);
            }
        }
    }
}
