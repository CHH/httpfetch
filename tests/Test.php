<?php

namespace chh\httpfetch\test;

use function chh\httpfetch\fetch;
use chh\httpfetch;
use Symfony\Component\Process\ProcessBuilder;

class ExampleTest extends \PHPUnit_Framework_TestCase
{
    private $server;

    public function setUp()
    {
        $cmd = sprintf('php -S localhost:8003 -t "%s"', __DIR__.'/web');
        $this->server = (new ProcessBuilder([$cmd]))
            ->disableOutput()
            ->getProcess();

        $this->server->start();
    }

    public function tearDown()
    {
        $this->server->stop();
    }

    public function testOverrideDefaultHandler()
    {
        $handler = new \GuzzleHttp\Ring\Client\CurlHandler;
        httpfetch\set_default_handler($handler);

        $this->assertEquals($handler, httpfetch\default_handler());
    }

    public function testFollowsRedirectByDefault()
    {
        $response = fetch('http://localhost:8003/redirect.php');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('Hello World', stream_get_contents($response['body']));
    }

    public function testDisableFollowRedirect()
    {
        $response = fetch('http://localhost:8003/redirect.php', ['follow_location' => false]);
        $this->assertEquals(301, $response['status']);
    }

    public function testHttpGet()
    {
        $response = httpfetch\get('http://localhost:8003/index.php', ['follow_location' => true]);

        $this->assertEquals(200, $response['status']);
        $this->assertEquals("Hello World", stream_get_contents($response['body']));
    }

    public function testHttpPost()
    {
        $response = httpfetch\post('http://localhost:8003/post.php', ['body' => 'foo']);

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('foo', stream_get_contents($response['body']));
    }

    public function testHttpPut()
    {
        $response = httpfetch\put('http://localhost:8003/post.php', ['body' => 'foo']);

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('foo', stream_get_contents($response['body']));
    }

    public function testHttpDelete()
    {
        $response = httpfetch\delete('http://localhost:8003/delete.php');

        $this->assertEquals(200, $response['status']);
    }

    public function testHttpHead()
    {
        $response = httpfetch\head('http://localhost:8003/head.php');

        $this->assertEquals(200, $response['status']);
    }

    public function testHttpOptions()
    {
        $response = httpfetch\options('http://localhost:8003/options.php');

        $this->assertEquals(200, $response['status']);
    }
}
