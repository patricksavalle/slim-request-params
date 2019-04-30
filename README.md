## PHP SLIM Request validation

_Needs PHP 7.x_

Validates request **query-parameters** and **body-parameters** ($_GET and $_BODY) and **HTTP-headers** using regular expressions. 
Adds a layer of security and self-documentation to your API-code. 
Uses the same syntax as SLIM uses for route-segments. 

Example:

    use SlimRequestParams\QueryParameters;

    $slimapp->get('', ...)
        ->add(new QueryParameters([
            '{text:\w+}',
            '{fromdate:\date}',
            '{distance:\float},0.0',
            '{orderby:(name|date)},name',
            '{reversed:\bool},false',
            '{offset:\int},1',
            '{count:\int},100',
        ]);

This would for example accept this request:

    GET yourdomain.com?text=airplane&fromdate=2016-10-10

And it would for example reject these requests:

    GET yourdomain.com?text=airplane&fromdate=2016-10-10&whatever=23
    GET yourdomain.com?text=airplane&fromdate=date
    GET yourdomain.com?fromdate=2016-10-10&whatever=23
    GET yourdomain.com?text=airplane&
    GET yourdomain.com
    
etc.

### 0. Install with [Composer](https://packagist.org/packages/patricksavalle/slim-request-params) ###

- Update your `composer.json` to require `patricksavalle/slim-request-params`.
- Run `composer install` to add slim-request-params your vendor folder.

    ```json
    {
      "require": {
        "patricksavalle/slim-request-params": "^1.0"
      }
    }
    ```

- Include in your source.

    ```php
    <?php
   
    require './vendor/autoload.php';
    ```

### 1. Add the middleware to the SLIM routes 

To validate request parameters:

    use SlimRequestParams\QueryParameters;

    $slimapp->get(...)
        ->add(new QueryParameters([
            '{author:[\w-. @]+}',
            '{orderby:\w+},id',
            '{reversed:1},1',
            '{offset:\int},1',
            '{count:\int},100',
            '{*}',
        ])

To validate body parameters:

    use SlimRequestParams\BodyParameters;

    $slimapp->post(...)
        ->add(new BodyParameters([
            '{recipient:.*}',
            '{sender:.*}',
            '{subject:.*}',
            '{timestamp:.*}',
            '{token:.*}',
            '{signature:.*}',
            '{*}',
        ]));

To validate request headers (make sure they are present and their values follows a specific pattern):

    use SlimRequestParams\RequestHeaders;

    $slimapp->post(...)
        ->add(new RequestHeaders([
            '{HTTP_REFERER:\url}',
            '{HTTP_CB_SIGNATURE:.*}',
        ]));

To forbid arguments to a route:

    $slimapp->get(...)
        ->add(new QueryParameters)
        
General format of a validation rule:

    {<name>:<regex>},<optional_default_value>

Missing parameters are set to the given default. 'null' or 'NULL' as default will be translated into a real null-value. 

Extra parameters generate an error unless the wildcard parameter is used: `{*}` in which 
case the extra parameters are passed without validation.

Accepts the RFC-standard query parameter array, not the PHP version:

    /someurl?A=10&a=11&a=12

For typed parameters and special formats there are the following keywords that can be used instead of the regex:

    \boolean
    \int
    \float
    \date
    \raw
    \base64json
    \url
    \email
    \country
    \nationality
    \timezone
    \currency
    \language
    \bitcoinaddress
    
These are only syntax checks! Also there is an accept 'anything else' argument:

    {*}

Optional parameters should get the default:
    
    \optional
    
_(See code example above)_

The RequestHeaders Middleware always accept 'anything else'.

### 2. Install the strategy for access to the validated arguments

Add the strategy that combines the url-, query- and post-parameters and request-headers into one object.

    $slimapp->getContainer()['foundHandler'] = function () {
        return new RequestResponseArgsObject;
    };        

### 3. Adapt your route handlers to the new strategy

A complete example.

    <?php
    
    declare(strict_types = 1);
    
    namespace YourApi;
    
    define("BASE_PATH", dirname(__FILE__));
    
    require BASE_PATH . '/vendor/autoload.php';
    
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    use \SlimRequestParams\BodyParameters;
    use \SlimRequestParams\QueryParameters;
    use \SlimRequestParams\RequestResponseArgsObject;
    
    $app = new \Slim\App;
    
    $app->getContainer()['foundHandler'] = function () {
        return new RequestResponseArgsObject;
    };
    
    $app->get('/hello/{name}', function (Request $request, Response $response, \stdClass $args) {
        $name = $args->name;
        $text = $args->text;
        $referer = $args->referer;
        $response->getBody()->write("$text, $name, $referer");
        return $response;
    })
        ->add(new RequestHeaders(['{HTTP_REFERER:\url']))
        ->add(new QueryParameters(['{text:[\w-.~@]+},Hello']));
    
    $app->run();

To retrieve or inspect the validated parameters from anywhere in your app just use:
    
    \SlimRequestParams\QueryParameters::get();
    \SlimRequestParams\BodyParameters::get();
    \SlimRequestParams\RequestHeaders::get();
