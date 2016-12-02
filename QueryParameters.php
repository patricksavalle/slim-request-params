<?php

declare(strict_types = 1);

namespace SlimRequestParams {

    require_once 'RequestParameters.php';

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;

    class QueryParameters extends RequestParameters
    {
        static protected $validated_parameters;

        public function __invoke(
            ServerRequestInterface $request,
            ResponseInterface $response,
            callable $next)
        : ResponseInterface
        {
            $this->validate($this->parse_str($request->getUri()->getQuery()));
            return $next($request, $response);
        }

        // allow for standard query parameter array e.g. ?a=10&a=11&a=12
        protected function parse_str(string $str): array
        {
            $arr = [];
            if (!empty($str)) {
                // split on outer delimiter
                $pairs = explode('&', $str);
                // loop through each pair
                foreach ($pairs as $i) {
                    // split into name and value
                    if (strpos($i, '=')) {
                        list($name, $value) = explode('=', $i, 2);
                        $value = urldecode($value);
                    } else {
                        $name = $i;
                        $value = null;
                    }
                    // if name already exists
                    if (isset($arr[$name])) {
                        // stick multiple values into an array
                        if (is_array($arr[$name])) {
                            $arr[$name][] = $value;
                        } else {
                            $arr[$name] = [$arr[$name], $value];
                        }
                    } // otherwise, simply stick it in a scalar
                    else {
                        $arr[$name] = $value;
                    }
                }
            }
            // return result array
            return $arr;
        }
    }
}