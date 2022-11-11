<?php

declare(strict_types = 1);

namespace SlimRequestParams {

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Slim\Interfaces\InvocationStrategyInterface;

    class RequestResponseArgsObject implements InvocationStrategyInterface
    {
        public function __invoke(
            callable $callable,
            ServerRequestInterface $request,
            ResponseInterface $response,
            array $routeArguments)
        : ResponseInterface
        {
            // merge validated query parameters into object
            foreach (RequestParameters::get() as $k => $v) {
                assert(!isset($routeArguments[$k]));
                $routeArguments[$k] = $v;
            }
            return $callable($request, $response, (object)$routeArguments);
        }
    }
}
