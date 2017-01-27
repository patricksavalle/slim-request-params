<?php

declare(strict_types = 1);

namespace SlimRequestParams {

    require_once 'RequestParameters.php';

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;

    class BodyParameters extends RequestParameters
    {
        static protected $validated_parameters;

        public function __invoke(
            ServerRequestInterface $request,
            ResponseInterface $response,
            callable $next)
        : ResponseInterface
        {
            $this->validate($request->getParsedBody() ?? []);
            return $next($request, $response);
        }
    }
}