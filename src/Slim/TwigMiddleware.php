<?php


namespace Blog\Slim;

use Twig\Environment;
use Blog\Twig\AssetExtension;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TwigMiddleware implements MiddlewareInterface
{

    private ?Environment $environment = null;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->environment->addExtension(new AssetExtension($request));
        return $handler->handle($request);
    }
}