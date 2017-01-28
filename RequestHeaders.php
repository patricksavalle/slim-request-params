<?php

/**
 * TRIPDRIVE.COM
 *
 * @link:       api.tripdrive.com
 * @copyright:  VCK TRAVEL BV, 2016
 * @author:     patrick@patricksavalle.com
 *
 * Note: use coding standards at http://www.php-fig.org/psr/
 */

declare(strict_types = 1);

namespace SlimRequestParams {

    require_once 'RequestParameters.php';

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;

    class RequestHeaders extends RequestParameters
    {
        static protected $validated_parameters;

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
