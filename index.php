<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$config = include 'config/db.php';

try {
    $connection = new PDO($config['dsn'], $config['user'], $config['pass']);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    echo 'Error подключения к БД ' . $exception->getMessage();
    die();
}

$postMapper = new \Blog\PostMapper($connection);


$loader = new \Twig\Loader\FilesystemLoader('template');
$view = new \Twig\Environment($loader);

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) use ($view) {
    $body = $view->render('index.twig');
    $response->getBody()->write($body);
    return $response;
});

$app->get('/about', function (Request $request, Response $response, $args) use ($view) {
    $body = $view->render('about.twig', ['name' => 'sd']);
    $response->getBody()->write($body);
    return $response;
});

$app->get('/{slug}', function (Request $request, Response $response, $args) use ($view, $postMapper) {
    $post = $postMapper->getBySlug($args['slug']);
    if (empty($post)) {
        $body = $view->render('not-found.twig');
    } else {
        $body = $view->render('post.twig', compact('post'));
    }
    $response->getBody()->write($body);
    return $response;
});

$app->run();