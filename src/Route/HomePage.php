<?php

namespace Blog\Route;

use Blog\LatestPosts;
use Twig\Environment;
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};
use Twig\Error\{LoaderError, RuntimeError, SyntaxError};

class HomePage
{
    private LatestPosts $latestPosts;
    private ?Environment $view;

    public function __construct(LatestPosts $latestPosts, Environment $environment)
    {
        $this->latestPosts = $latestPosts;
        $this->view = $environment;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function execute(Request $request, Response $response): Response
    {
        $posts = $this->latestPosts->get(3);
        array_walk($posts, fn(&$item) => $item['file_exists'] = file_exists(ROOT_DIR . '/' . $item['image_path']) && !empty($item['image_path']));
        $body = $this->view->render('index.twig', ['posts' => $posts]);
        $response->getBody()->write($body);
        return $response;
    }
}