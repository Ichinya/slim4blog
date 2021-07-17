<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function DI\get;
use function DI\autowire;

return [
    FilesystemLoader::class => autowire()->constructorParameter('paths', 'template'),
    Environment::class => autowire()->constructorParameter('loader', get(FilesystemLoader::class))
];