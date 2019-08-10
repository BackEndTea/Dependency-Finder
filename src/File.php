<?php

declare(strict_types=1);

namespace Depend;

class File
{
    /**
     * Array of all classes/functions declared by this file
     *
     * @var array<string>
     */
    public $declares = [];

    /**
     * Array of all the class/function dependencies of this file
     *
     * @var array<string>
     */
    public $dependencies = [];

    /**
     * @param array<string> $delcares
     * @param array<string> $dependencies
     */
    public function __construct(array $delcares = [], array $dependencies = [])
    {
        $this->declares     = $delcares;
        $this->dependencies = $dependencies;
    }
}
