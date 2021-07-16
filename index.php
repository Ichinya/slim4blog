<?php

use Blog\LatestPosts;
use Blog\PostMapper;
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

$loader = new \Twig\Loader\FilesystemLoader('template');
$view = new \Twig\Environment($loader);

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response) use ($view, $connection) {
    $latestPosts = new LatestPosts($connection);
    $posts = $latestPosts->get(3);
    array_walk($posts, fn(&$item) => $item['file_exists'] = file_exists(__DIR__ . '/' . $item['image_path']) && !empty($item['image_path']));
    $body = $view->render('index.twig', ['posts' => $posts]);
    $response->getBody()->write($body);
    return $response;
});

$app->get('/about', function (Request $request, Response $response, $args) use ($view) {
    $body = $view->render('about.twig', ['name' => 'sd']);
    $response->getBody()->write($body);
    return $response;
});

$app->get('/blog[/{page}]', function (Request $request, Response $response, $args) use ($view, $connection) {

    $page = $args['page'] ?? 1;
    $limit = 5;

    $posts = new PostMapper($connection);
    $posts = $posts->getList($page, $limit, 'DESC');
    array_walk($posts, fn(&$item) => $item['file_exists'] = file_exists(__DIR__ . '/' . $item['image_path']) && !empty($item['image_path']));

    $body = $view->render('blog.twig', ['posts' => $posts]);
    $response->getBody()->write($body);
    return $response;
});

$app->get('/{slug}', function (Request $request, Response $response, $args) use ($view, $connection) {
    $postMapper = new PostMapper($connection);
    $post = $postMapper->getBySlug($args['slug']);
    if (empty($post)) {
        $body = $view->render('not-found.twig');
    } else {
        $post['file_exists'] = file_exists(__DIR__ . '/' . $post['image_path']) && !empty($post['image_path']);
        $body = $view->render('post.twig', compact('post'));
    }
    $response->getBody()->write($body);
    return $response;
});

$app->run();