<?php

namespace Blog\Route;

use Blog\PostMapper;
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};
use Twig\Environment;

class PostPage
{

    private ?PostMapper $postMapper;
    private ?Environment $view;

    public function __construct(PostMapper $postMapper, Environment $environment)
    {
        $this->postMapper = $postMapper;
        $this->view = $environment;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $post = $this->postMapper->getBySlug($args['slug']);
        if (empty($post)) {
            $body = $this->view->render('not-found.twig');
        } else {
            $post['file_exists'] = file_exists(ROOT_DIR . '/' . $post['image_path']) && !empty($post['image_path']);
            $body = $this->view->render('post.twig', compact('post'));
        }
        $response->getBody()->write($body);
        return $response;
    }
}