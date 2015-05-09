<?php

namespace chh\httpfetch\test;

use function chh\httpfetch\fetch;
use chh\httpfetch;

class ExampleTest extends \PHPUnit_Framework_TestCase
{
    public function testOverrideDefaultHandler()
    {
        $handler = new \GuzzleHttp\Ring\Client\CurlHandler;
        httpfetch\default_handler($handler);

        $this->assertEquals($handler, httpfetch\default_handler());
    }

    /**
     * Test that true does in fact equal true
     */
    public function testTrueIsTrue()
    {
        $response = fetch('https://google.at', ['http_method' => 'GET']);
        $this->assertEquals(200, $response['status']);
    }

    public function testHttpGet()
    {
        $response = httpfetch\get('https://google.at');
        $this->assertEquals(200, $response['status']);
    }
}
