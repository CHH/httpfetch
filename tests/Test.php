<?php

namespace chh\httpfetch\test;

use chh\httpfetch;

class ExampleTest extends \PHPUnit_Framework_TestCase
{
    public function testOverrideDefaultHandler()
    {
        $handler = new \GuzzleHttp\Ring\Client\CurlHandler;
        httpfetch\set_default_handler($handler);

        $this->assertEquals($handler, httpfetch\default_handler());

        httpfetch\set_default_handler(null);
    }

    public function testFollowsRedirectByDefault()
    {
        $response = httpfetch\fetch('http://localhost:8003/redirect.php');

        $this->assertArrayNotHasKey('error', $response);
        $this->assertEquals(200, $response['status']);
        $this->assertEquals('Hello World', stream_get_contents($response['body']));
    }

    public function testDisableFollowRedirect()
    {
        $response = httpfetch\fetch('http://localhost:8003/redirect.php', ['follow_location' => false]);
        $this->assertArrayNotHasKey('error', $response);
        $this->assertEquals(301, $response['status']);
    }

    public function testBasicAuth()
    {
        $response = httpfetch\get('http://foo:bar@localhost:8003/basic-auth.php');

        $this->assertArrayNotHasKey('error', $response);
        $this->assertEquals(200, $response['status']);
        $this->assertEquals("foo:bar", stream_get_contents($response['body']));
    }

    public function testBasicAuthWithEmptyPassword()
    {
        $response = httpfetch\get('http://foo:@localhost:8003/basic-auth.php');

        $this->assertArrayNotHasKey('error', $response);
        $this->assertEquals(200, $response['status']);
        $this->assertEquals("foo:", stream_get_contents($response['body']));
    }

    public function testBasicAuthWithAuthOption()
    {
        $response = httpfetch\get('http://localhost:8003/basic-auth.php', ['auth' => ['foo', 'bar']]);

        $this->assertArrayNotHasKey('error', $response);
        $this->assertEquals(200, $response['status']);
        $this->assertEquals("foo:bar", stream_get_contents($response['body']));

        $response = httpfetch\get('http://localhost:8003/basic-auth.php', ['auth' => ['foo', '']]);

        $this->assertArrayNotHasKey('error', $response);
        $this->assertEquals(200, $response['status']);
        $this->assertEquals("foo:", stream_get_contents($response['body']));
    }

    public function testHttpGet()
    {
        $response = httpfetch\get('http://localhost:8003/index.php', ['follow_location' => true]);

        $this->assertArrayNotHasKey('error', $response);
        $this->assertEquals(200, $response['status']);
        $this->assertEquals("Hello World", stream_get_contents($response['body']));
    }

    public function testHttpPost()
    {
        $response = httpfetch\post('http://localhost:8003/post.php', ['body' => 'foo']);

        $this->assertArrayNotHasKey('error', $response);
        $this->assertEquals(200, $response['status']);
        $this->assertEquals('foo', stream_get_contents($response['body']));
    }

    public function testHttpPut()
    {
        $response = httpfetch\put('http://localhost:8003/post.php', ['body' => 'foo']);

        $this->assertArrayNotHasKey('error', $response);
        $this->assertEquals(200, $response['status']);
        $this->assertEquals('foo', stream_get_contents($response['body']));
    }

    public function testHttpDelete()
    {
        $response = httpfetch\delete('http://localhost:8003/delete.php');

        $this->assertArrayNotHasKey('error', $response);
        $this->assertEquals(200, $response['status']);
    }

    public function testHttpHead()
    {
        $response = httpfetch\head('http://localhost:8003/head.php');

        $this->assertArrayNotHasKey('error', $response);
        $this->assertEquals(200, $response['status']);
    }

    public function testHttpOptions()
    {
        $response = httpfetch\options('http://localhost:8003/options.php');

        $this->assertArrayNotHasKey('error', $response);
        $this->assertEquals(200, $response['status']);
    }

    function testNonHttpUrl()
    {
        $response = httpfetch\fetch('ftp://example.com');

        $this->assertInstanceOf('\Exception', $response['error']);
    }
}
