<?php

namespace SlimRequestParams\Tests;

use SlimRequestParams\QueryParameters;

class QueryParametersTest extends \PHPUnit_Framework_TestCase
{

    public function call(array $rules, string $query, bool $expect_exception)
    {
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo/bar',
            'QUERY_STRING' => $query,
            'SERVER_NAME' => 'example.com',
            'CONTENT_TYPE' => 'application/json;charset=utf8',
            'CONTENT_LENGTH' => 15
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();

        if ($expect_exception) {
            $this->expectException(\Throwable::class);
        }
        $queryparams = new \SlimRequestParams\QueryParameters($rules);
        $queryparams($request, $response, function ($request, $response) {
            return $response;
        });
        return QueryParameters::get();
    }

    public function testIncorrect9()
    {
        $this->call(['{foo:\boolean}'], 'foo=0.0', true);
    }

    // Test arrays

    public function testIncorrect0()
    {
        $this->call(['{foo:\int}'], 'foo[]=0&foo[]=1&foo[]=2', true);
    }
    public function testIncorrect1()
    {
        $this->call(['{foo:\int}'], 'foo=0?foo=1?foo=2', true);
    }
    public function testIncorrect2()
    {
        $this->call(['{foo:\int}'], 'foo=0&foo=a', true);
    }

    // Test invalid values

    public function testIncorrect3()
    {
        $this->call(['foo:\boolean,true'], 'foo=', true);
    }
    public function testIncorrect4()
    {
        $this->call(['{foo:\boolean}'], '', true);
    }
    public function testIncorrect10()
    {
        var_dump($this->call(['{foo:\boolean}'], 'foo=True', true));
    }
    public function testIncorrect11()
    {
        $this->call(['{foo:\boolean}'], 'foo=False', true);
    }
    public function testIncorrect12()
    {
        $this->call(['{foo:\boolean}'], 'foo=FALSE0', true);
    }
    public function testIncorrect13()
    {
        $this->call(['{foo:\int}'], 'foo=a', true);
    }
    public function testIncorrect14()
    {
        $this->call(['{foo:\int}'], 'foo=0.0', true);
    }
    public function testIncorrect16()
    {
        $this->call(['{foo:\float}'], 'foo=a', true);
    }
    public function testIncorrect17()
    {
        $this->call(['{foo:\float}'], 'foo=0,0', true);
    }
    public function testIncorrect18()
    {
        $this->call(['{foo:\float}'], 'foo=5E2.3', true);
    }
    public function testIncorrect19()
    {
        $this->call(['{foo:\email}'], 'foo=Patrick Savalle <patrick@patricksavalle.com>', true);
    }
    public function testIncorrect20()
    {
        $this->call(['{foo:\email}'], 'foo=@patrick.savalle@patricksavalle.com', true);
    }
    public function testIncorrect21()
    {
        $this->call(['{foo:\email}'], 'foo=patrick&savale@patricksavalle.com', true);
    }
    public function testIncorrect22()
    {
        $this->call(['{foo:\email}'], 'foo=patrick@patrick', true);
    }
    public function testIncorrect23()
    {
        $this->call(['{foo:\date}'], 'foo=2014.12.12', true);
    }
    public function testIncorrect24()
    {
        $this->call(['{foo:\date}'], 'foo=10 appie 1990', true);
    }
    public function testIncorrect25()
    {
        $this->call(['{foo:\timezone}'], 'foo=Europe\Amsterdam', true);
    }
    public function testIncorrect26()
    {
        $this->call(['{foo:\timezone}'], 'foo=+01', true);
    }
    public function testIncorrect27()
    {
        $this->call(['{foo:\domain}'], 'foo=mobbr.com/', true);
    }
    public function testIncorrect28()
    {
        $this->call(['{foo:\domain}'], 'foo=https://mobbr.com', true);
    }

    // ------------------------------------------------------------------------

    public function testCorrect1()
    {
        $this->call(['{foo:\int}'], 'foo=0&foo=1&foo=2', false);
    }
    public function testCorrect2()
    {
        $this->call(['{foo:\date}'], 'foo=2014-12-12&foo=2014-12-12&foo=2014-12-12', false);
    }
    public function testCorrect3()
    {
        $this->call(['{foo:\boolean},true'], '', false);
    }
    public function testCorrect4()
    {
        $this->call(['{foo:\boolean},true'], 'foo', false);
    }
    public function testCorrect5()
    {
        $this->call(['{foo:\boolean},true'], 'foo=', false);
    }
    public function testCorrect6()
    {
        $this->call(['{foo:\boolean}'], 'foo=0', false);
    }
    public function testCorrect7()
    {
        $this->call(['{foo:\boolean}'], 'foo=1', false);
    }
    public function testCorrect8()
    {
        $this->call(['{foo:\boolean}'], 'foo=true', false);
    }
    public function testCorrect9()
    {
        $this->call(['{foo:\boolean}'], 'foo=false', false);
    }
    public function testCorrect10()
    {
        $this->call(['{foo:\boolean}'], 'foo=TRUE', false);
    }
    public function testCorrect12()
    {
        $this->call(['{foo:\boolean}'], 'foo=FALSE', false);
    }
    public function testCorrect13()
    {
        $this->call(['{foo:\int}'], 'foo=1', false);
    }
    public function testCorrect14()
    {
        $this->call(['{foo:\int}'], 'foo=+1', false);
    }
    public function testCorrect15()
    {
        $this->call(['{foo:\int}'], 'foo=-1', false);
    }
    public function testCorrect16()
    {
        $this->call(['{foo:\int}'], 'foo=123456789', false);
    }
    public function testCorrect17()
    {
        $this->call(['{foo:\float}'], 'foo=1', false);
    }
    public function testCorrect18()
    {
        $this->call(['{foo:\float}'], 'foo=+1', false);
    }
    public function testCorrect19()
    {
        $this->call(['{foo:\float}'], 'foo=-1', false);
    }
    public function testCorrect20()
    {
        $this->call(['{foo:\float}'], 'foo=.1', false);
    }
    public function testCorrect21()
    {
        $this->call(['{foo:\float}'], 'foo=+.1', false);
    }
    public function testCorrect22()
    {
        $this->call(['{foo:\float}'], 'foo=-.1', false);
    }
    public function testCorrect23()
    {
        $this->call(['{foo:\float}'], 'foo=1.0', false);
    }
    public function testCorrect24()
    {
        $this->call(['{foo:\float}'], 'foo=+1.0', false);
    }
    public function testCorrect25()
    {
        $this->call(['{foo:\float}'], 'foo=-1.0', false);
    }
    public function testCorrect26()
    {
        $this->call(['{foo:\float}'], 'foo=-5E-2', false);
    }
    public function testCorrect11()
    {
        $this->call(['{foo:\float}'], 'foo=-05.34E-02', false);
    }
    public function testCorrect27()
    {
        $this->call(['{foo:\float}'], 'foo=5E2', false);
    }
    public function testCorrect28()
    {
        $this->call(['{foo:\email}'], 'foo=patrick@patricksavalle.com', false);
    }
    public function testCorrect29()
    {
        $this->call(['{foo:\email}'], 'foo=patrick.savalle@patricksavalle.com', false);
    }
    public function testCorrect30()
    {
        $this->call(['{foo:\email}'], 'foo=patrick_savalle@patricksavalle.com', false);
    }
    public function testCorrect31()
    {
        $this->call(['{foo:\date}'], 'foo=2014/12/12', false);
    }
    public function testCorrect32()
    {
        $this->call(['{foo:\date}'], 'foo=2014-1-1', false);
    }
    public function testCorrect33()
    {
        $this->call(['{foo:\date}'], 'foo=10 august 1990', false);
    }
    public function testCorrect34()
    {
        $this->call(['{foo:\date}'], 'foo=2014-1-1 10:10', false);
    }
    public function testCorrect35()
    {
        $this->call(['{foo:\date}'], 'foo=2014-01-01T10:10:10-01:00', false);
    }
    public function testCorrect36()
    {
        $this->call(['{foo:\timezone}'], 'foo=Europe/Amsterdam', false);
    }
    public function testCorrect37()
    {
        $this->call(['{foo:\timezone}'], 'foo=UTC', false);
    }
    public function testCorrect38()
    {
        $this->call(['{foo:\timezone}'], 'foo=zulu', false);
    }
    public function testCorrect39()
    {
        $this->call(['{foo:\domain}'], 'foo=api.mobbr.com', false);
    }
    public function testCorrect40()
    {
        $this->call(['{foo:\domain}'], 'foo=mobbr.com', false);
    }

    // Test combinations
    public function testCorrect45()
    {
        $this->call([
            '{a:\timezone},Zulu',
            '{b:\datetime},2014-01-01T10:10:10-01:00',
            '{c:\int}',
            '{d:\int},1',
            '{e:\float},1.0',
        ], 'a=zulu&c=1&d=2&e=1&e=2', false);
    }

    // Test types and formats

    public function testCorrect50()
    {
        $this->assertEquals('string',gettype($this->call(['{foo:\date}'], 'foo=2014-1-1 10:10', false)->foo));
        $this->assertRegExp('@\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}@',$this->call(['{foo:\date}'], 'foo=2014-1-1 10:10', false)->foo);
    }

    public function testCorrect51()
    {
        $this->assertEquals('integer',gettype($this->call(['{foo:\int}'], 'foo=10', false)->foo));
        $this->assertEquals('NULL',gettype($this->call(['{foo:\int}'], 'foo=', false)->foo));
    }

    public function testCorrect52()
    {
        $this->assertEquals('double',gettype($this->call(['{foo:\float}'], 'foo=10.0', false)->foo));
    }

    public function testCorrect53()
    {
        $this->assertEquals('boolean',gettype($this->call(['{foo:\boolean}'], 'foo=true', false)->foo));
    }

    public function testCorrect54()
    {
        $this->assertEquals('array',gettype($this->call(['{foo:\boolean}'], 'foo=true&foo=true', false)->foo));
    }

    // Test format

    public function testIncorrect60()
    {
        $this->call(['foo:\float'], 'foo', true);
    }

    public function testIncorrect61()
    {
        $this->call(['{foo:\float'], 'foo', true);
    }

    public function testIncorrect62()
    {
        $this->call(['{foo:}'], 'foo=a', true);
    }

    public function testIncorrect63()
    {
        $this->call(['{foo:}'], 'foo', true);
    }

    public function testIncorrect64()
    {
        $this->call(['{:\int}'], 'foo', true);
    }


}