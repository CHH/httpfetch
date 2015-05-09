<?php

namespace chh\httpfetch;

use GuzzleHttp\Ring\Client;
use GuzzleHttp\Ring\Core;
use League\Url\Url;

/**
 * Set or get the HTTP handler.
 *
 * @param  callable $handler Guzzle Ring Handler
 * @return callable
 */
function default_handler(callable $handler = null)
{
    static $_handler;

    if (null === $_handler) {
        if (null !== $handler) {
            $_handler = $handler;
        } elseif (extension_loaded('curl')) {
            $_handler = new Client\CurlMultiHandler;
        } else {
            $_handler = new Client\StreamHandler;
        }
    }

    return $_handler;
}

/**
 * Fetches the URL
 *
 * @param string $url
 * @param array $options Ring Request
 * @return array Ring Response
 */
function fetch($url, $options = [])
{
    $url = Url::createFromUrl((string) $url);

    if (isset($options['handler'])) {
        $handler = $options['handler'];
        unset($options['handler']);
    } else {
        $handler = default_handler();
    }

    $request = array_merge([
        'uri' => (string) $url->getPath(),
        'query_string' => (string) $url->getQuery(),
        'scheme' => $url->getScheme(),
        'http_method' => 'GET',
    ], $options);

    if (!Core::hasHeader($request, 'Host')) {
        $request['headers']['host'] = [(string) $url->getHost()];
    }

    if ($user = $url->getUser()) {
        $request['headers']['authorization'] = ['Basic '.base64_encode((string) $user.':'.$url->getPass())];
    }

    $response = $handler($request);

    if (in_array($response['status'], [301, 302], true)) {
        return fetch(Core::firstHeader($response, 'location'), $options);
    }

    return $response;
}

/**
 * Fetches the URL with a HTTP GET request
 *
 * @param string $url
 * @param array $options Ring Request
 */
function get($url, $options = [])
{
    $options['http_method'] = 'GET';
    return fetch($url, $options);
}

/**
 * Fetches the URL with a HTTP POST request
 *
 * @param string $url
 * @param array $options Ring Request
 */
function post($url, $options = [])
{
    $options['http_method'] = 'POST';
    return fetch($url, $options);
}

/**
 * Fetches the URL with a HTTP PUT request
 *
 * @param string $url
 * @param array $options Ring Request
 */
function put($url, $options = [])
{
    $options['http_method'] = 'PUT';
    return fetch($url, $options);
}

/**
 * Fetches the URL with a HTTP DELETE request
 *
 * @param string $url
 * @param array $options Ring Request
 */
function delete($url, $options = [])
{
    $options['http_method'] = 'DELETE';
    return fetch($url, $options);
}

/**
 * Fetches the URL with a HTTP OPTIONS request
 *
 * @param string $url
 * @param array $options Ring Request
 */
function options($url, $options)
{
    $options['http_method'] = 'OPTIONS';
    return fetch($url, $options);
}

/**
 * Fetches the URL with a HTTP HEAD request
 *
 * @param string $url
 * @param array $options Ring Request
 */
function head($url, $options)
{
    $options['http_method'] = 'HEAD';
    return fetch($url, $options);
}
