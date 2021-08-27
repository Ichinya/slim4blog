<?php

use Blog\Database;
use Blog\Twig\AssetExtension;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function DI\get;
use function DI\autowire;

return [
    'server.params' => $_SERVER,
    FilesystemLoader::class => autowire()
        ->constructorParameter('paths', 'template'),

    Environment::class => autowire()
        ->constructorParameter('loader', get(FilesystemLoader::class))
        ->method('addExtension', get(AssetExtension::class)),

    Database::class => autowire()
        ->constructorParameter('connection', get(PDO::class)),

    PDO::class => autowire()
        ->constructor(
            getenv('DATABASE_DSN'),
            getenv('DATABASE_USERNAME'),
            getenv('DATABASE_PASSWORD'),
            []
        )
        ->method('setAttribute', PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION)
        ->method('setAttribute', PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC),

    AssetExtension::class => autowire()
        ->constructorParameter('serverParams', get('server.params'))
];