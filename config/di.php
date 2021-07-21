<?php

use Blog\Database;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function DI\get;
use function DI\autowire;

return [
    FilesystemLoader::class => autowire()
        ->constructorParameter('paths', 'template'),

    Environment::class => autowire()
        ->constructorParameter('loader', get(FilesystemLoader::class)),

    Database::class => autowire()
        ->constructorParameter('connection', get(PDO::class)),

    PDO::class => autowire()
        ->constructorParameter('dsn', getenv('DATABASE_DSN'))
        ->constructorParameter('username', getenv('DATABASE_USERNAME'))
        ->constructorParameter('passwd', getenv('DATABASE_PASSWORD'))
        ->constructorParameter('options', [])
];