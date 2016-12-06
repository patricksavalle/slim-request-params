<?php

namespace SlimRequestParams\Tests;

class QueryParametersTest extends \PHPUnit_Framework_TestCase {

    public function testIncorrectQueryParameters() {

        $rules = [
            '{abc:.+}',
            '{foo:[0-9]+}'
        ];

        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo/bar',
            'QUERY_STRING' => 'abc=123&foo=bar',
            'SERVER_NAME' => 'example.com',
            'CONTENT_TYPE' => 'application/json;charset=utf8',
            'CONTENT_LENGTH' => 15
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();

        $this->expectException(\InvalidArgumentException::class);

        $queryparams = new \SlimRequestParams\QueryParameters($rules);
        $queryparams($request, $response, function ($request, $response) {
            return $response;
        });
    }

    public function testCorrectQueryParameters() {

        $rules = [
            '{abc:.+}',
            '{foo:[0-9]+}'
        ];

        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo/bar',
            'QUERY_STRING' => 'abc=123&foo=456',
            'SERVER_NAME' => 'example.com',
            'CONTENT_TYPE' => 'application/json;charset=utf8',
            'CONTENT_LENGTH' => 15
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();

        $queryparams = new \SlimRequestParams\QueryParameters($rules);
        $queryparams($request, $response, function ($request, $response) {
            return $response;
        });

        $params = \SlimRequestParams\QueryParameters::get();

        $this->assertEquals($params->abc, 123);
        $this->assertEquals($params->foo, 456);
    }
}