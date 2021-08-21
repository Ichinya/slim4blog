<?php

use Blog\Route\{AboutPage, BlogPage, HomePage, PostPage};
use DevCoder\DotEnv;
use DI\ContainerBuilder;
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

$app->get('/', HomePage::class . ':execute');

$app->get('/about', AboutPage::class);

$app->get('/blog[/{page}]', BlogPage::class);

$app->get('/{slug}', PostPage::class);

$app->run();