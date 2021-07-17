<?php

namespace Blog\Route;

use Blog\LatestPosts;
use Blog\PostMapper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

class BlogPage
{

    private ?PostMapper $postMapper;
    private ?Environment $view;

    public function __construct(PostMapper $postMapper, Environment $environment)
    {
        $this->postMapper = $postMapper;
        $this->view = $environment;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $page = (int)($args['page'] ?? 1);
        $limit = 5;

        $posts = $this->postMapper->getList($page, $limit, 'DESC');
        array_walk($posts, fn(&$item) => $item['file_exists'] = file_exists(ROOT_DIR . '/' . $item['image_path']) && !empty($item['image_path']));

        $totalCount = $this->postMapper->getTotalCount();

        $body = $this->view->render('blog.twig', [
            'posts' => $posts,
            'pagination' => [
                'current' => $page,
                'paging' => ceil($totalCount / $limit)
            ]
        ]);
        $response->getBody()->write($body);
        return $response;
    }
}