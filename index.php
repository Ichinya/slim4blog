<?php

use Blog\Route\{AboutPage, BlogPage, HomePage, PostPage};
use Blog\Slim\TwigMiddleware;
use DevCoder\DotEnv;
use DI\{ContainerBuilder, DependencyException, NotFoundException};
use Slim\Factory\AppFactory;

const ROOT_DIR = __DIR__;
require ROOT_DIR . '/vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions('config/di.php');

(new DotEnv(ROOT_DIR . '/.env'))->load();

try {
    $container = $builder->build();
} catch (Exception $e) {
    echo 'Ошибка создания контейнера ' . $e->getMessage();
    die();
}

AppFactory::setContainer($container);

$app = AppFactory::create();

try {
    $app->add($container->get(TwigMiddleware::class));
} catch (DependencyException | NotFoundException | Exception $e) {
    echo 'Ошибка получения нужного контейнера ' . $e->getMessage();
}

$app->get('/', HomePage::class . ':execute');

$app->get('/about', AboutPage::class);

$app->get('/blog[/{page}]', BlogPage::class);

$app->get('/{slug}', PostPage::class);

$app->run();