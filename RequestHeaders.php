<?php

declare(strict_types = 1);

namespace SlimRequestParams {

    require_once 'RequestParameters.php';

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;

    class RequestHeaders extends RequestParameters
    {
        public function __construct(array $rules = [])
        {
            parent::__construct($rules);
            // standard allow all headers
            $this->rules[] = '{*}';
        }

        public function __invoke(
            ServerRequestInterface $request,
            ResponseInterface $response,
            callable $next)
        : ResponseInterface
        {
            $this->validate($request->getHeaders());
            return $next($request, $response);
        }
    }
}
