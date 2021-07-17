<?php


namespace Blog\Route;

use Blog\Database;
use Blog\LatestPosts;
use Twig\Environment;
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};

class HomePage
{
    private LatestPosts $latestPosts;
    private ?Environment $view;

    public function __construct(LatestPosts $latestPosts, Environment $environment)
    {
        $this->latestPosts = $latestPosts;
        $this->view = $environment;
    }

    public function execute(Request $request, Response $response): Response
    {
        $posts = $this->latestPosts->get(3);
        array_walk($posts, fn(&$item) => $item['file_exists'] = file_exists(ROOT_DIR . '/' . $item['image_path']) && !empty($item['image_path']));
        $body = $this->view->render('index.twig', ['posts' => $posts]);
        $response->getBody()->write($body);
        return $response;
    }
}