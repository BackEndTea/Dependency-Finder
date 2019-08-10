# PHP Depend resolver

## What is this

This library allows you to find the dependencies of a php file.

It was created to help with another libary, that allows you to run tests for only changes files (and their dependencies)

## Usage
```php
<?php
require_once __DIR__.'/vendor/autoload.php';

use Depend\DependencyFinder;


$finder = new DependencyFinder([__DIR__.'/src/',  './vendor/psr/container/src', __DIR__.'/tests']);

$finder->build();

$deps = $finder->getAllFilesDependingOn(['./tests/Fixtures/Circular/A.php']);

foreach ($deps as $dep) {
    var_dump($dep);
}

$finder->reBuild(['./src/Domain/User.php', './tests/Domain/User.php', './src/functions.php']);
```
