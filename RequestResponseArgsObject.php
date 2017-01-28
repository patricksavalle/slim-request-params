<?php

declare(strict_types = 1);

namespace SlimRequestParams {

    require_once 'BodyParameters.php';
    require_once 'QueryParameters.php';

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
            foreach (QueryParameters::get() as $k => $v) {
                assert(!isset($routeArguments[$k]));
                $routeArguments[$k] = $v;
            }
            // merge validated body parameters into object
            foreach (BodyParameters::get() as $k => $v) {
                assert(!isset($routeArguments[$k]));
                $routeArguments[$k] = $v;
            }
            // merge validated body parameters into object
            foreach (RequestHeaders::get() as $k => $v) {
                assert(!isset($routeArguments[$k]));
                $routeArguments[$k] = $v;
            }
            return $callable($request, $response, (object)$routeArguments);
        }
    }
}
