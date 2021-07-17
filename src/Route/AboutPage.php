<?php

namespace Blog\Route;

use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};
use Twig\Environment;

class AboutPage
{

    private ?Environment $view;

    public function __construct(Environment $environment)
    {
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
    public function __invoke(Request $request, Response $response): Response
    {
        $body = $this->view->render('about.twig', ['name' => 'Ichi']);
        $response->getBody()->write($body);
        return $response;
    }
}