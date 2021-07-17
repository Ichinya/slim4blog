<?php

use Blog\{Database, PostMapper, LatestPosts, Slim\TwigMiddleware};
use DevCoder\DotEnv;
use DI\{ContainerBuilder, DependencyException, NotFoundException};
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};
use Slim\Factory\AppFactory;
use Twig\Environment;

require __DIR__ . '/vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions('config/di.php');

(new DotEnv(__DIR__ . '/.env'))->load();

try {
    $container = $builder->build();
} catch (Exception $e) {
    echo 'Ошибка создания контейнера ' . $e->getMessage();
    die();
}

AppFactory::setContainer($container);

$app = AppFactory::create();

try {
    $view = $container->get(Environment::class);
} catch (DependencyException | NotFoundException | Exception $e) {
    echo 'Ошибка получения нужного контейнера ' . $e->getMessage();
    die();
}

try {
    $connection = $container->get(Database::class)->getConnection();
} catch (DependencyException | NotFoundException | Exception $e) {
    echo 'Ошибка получения нужного контейнера ' . $e->getMessage();
    die();
}

$app->add(new TwigMiddleware($view));


$app->get('/', function (Request $request, Response $response) use ($view, $connection) {
    $latestPosts = new LatestPosts($connection);
    $posts = $latestPosts->get(3);
    array_walk($posts, fn(&$item) => $item['file_exists'] = file_exists(__DIR__ . '/' . $item['image_path']) && !empty($item['image_path']));
    $body = $view->render('index.twig', ['posts' => $posts]);
    $response->getBody()->write($body);
    return $response;
});

$app->get('/about', function (Request $request, Response $response, $args) use ($view) {
    $body = $view->render('about.twig', ['name' => 'Ichi']);
    $response->getBody()->write($body);
    return $response;
});

$app->get('/blog[/{page}]', function (Request $request, Response $response, $args) use ($view, $connection) {

    $page = (int)($args['page'] ?? 1);
    $limit = 5;

    $postMapper = new PostMapper($connection);
    $posts = $postMapper->getList($page, $limit, 'DESC');
    array_walk($posts, fn(&$item) => $item['file_exists'] = file_exists(__DIR__ . '/' . $item['image_path']) && !empty($item['image_path']));

    $totalCount = $postMapper->getTotalCount();

    $body = $view->render('blog.twig', [
        'posts' => $posts,
        'pagination' => [
            'current' => $page,
            'paging' => ceil($totalCount / $limit)
        ]
    ]);
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