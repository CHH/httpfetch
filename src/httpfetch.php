<?php

namespace chh\httpfetch;

use GuzzleHttp\Ring\Client;
use GuzzleHttp\Ring\Core;

/**
 * Sets a new default HTTP handler
 *
 * @param callable $handler Guzzle Ring Client Handler
 */
function set_default_handler(callable $handler = null)
{
    global $CHH_HTTP_FETCH_HANDLER;
    $CHH_HTTP_FETCH_HANDLER = $handler;
}

/**
 * Returns the current default HTTP handler
 *
 * @return callable
 */
function default_handler()
{
    global $CHH_HTTP_FETCH_HANDLER;

    if (null === $CHH_HTTP_FETCH_HANDLER) {
        if (extension_loaded('curl') && function_exists('curl_reset')) {
            $CHH_HTTP_FETCH_HANDLER = new Client\CurlMultiHandler;
        } else {
            $CHH_HTTP_FETCH_HANDLER = new Client\StreamHandler;
        }
    }

    return $CHH_HTTP_FETCH_HANDLER;
}

function _default_options(array $options)
{
    $options = array_merge([
        'follow_location' => true,
        'max_redirects' => 10,
        'http_method' => 'GET',
    ], $options);

    if ($options['follow_location']) {
        $options['client']['curl'][CURLOPT_FOLLOWLOCATION] = true;
        $options['client']['curl'][CURLOPT_MAXREDIRS] = (int) $options['max_redirects'];

        $options['client']['stream_context']['http']['follow_location'] = 1;
        $options['client']['stream_context']['http']['max_redirects'] = (int) $options['max_redirects'];
    }
    unset($options['follow_location']);
    unset($options['max_redirects']);

    return $options;
}

/**
 * Fetches the HTTP URL
 *
 * @api
 * @param string $url
 * @param array $options See Guzzle Ring Request
 * @return array Ring Response
 */
function fetch($url, array $options = [])
{
    $urlComponents = parse_url($url);
    $request = [];

    $options = _default_options($options);

    if (isset($options['handler'])) {
        $handler = $options['handler'];
        unset($options['handler']);
    } else {
        $handler = default_handler();
    }

    $request = array_merge([
        'uri' => isset($urlComponents['path']) ? $urlComponents['path'] : '/',
        'scheme' => $urlComponents['scheme'],
    ], $options);

    if (isset($urlComponents['query'])) {
        $request['query_string'] = $urlComponents['query'];
    }

    if (!Core::hasHeader($request, 'host')) {
        $host = $urlComponents['host'];

        if (isset($urlComponents['port'])
            && $urlComponents['port'] !== 80 && $urlComponents['port'] !== 443) {
            $host .= ':'.$urlComponents['port'];
        }

        $request['headers']['host'] = [$host];
    }

    if (!Core::hasHeader($request, 'authorization')) {
        if (isset($urlComponents['user'])) {
            $user = $urlComponents['user'];
            $password = isset($urlComponents['pass']) ? $urlComponents['pass'] : '';
        } elseif (isset($options['auth'])) {
            @list($user, $password) = $options['auth'];
        }

        if (isset($user) && isset($password)) {
            $request['headers']['authorization'] = [
                'Basic '.base64_encode($user.':'.$password)
            ];
        }
    }

    $response = $handler($request);

    return $response;
}

/**
 * Fetches the URL with a HTTP GET request
 *
 * @api
 * @param string $url
 * @param array $options Ring Request
 */
function get($url, array $options = [])
{
    $options['http_method'] = 'GET';
    return fetch($url, $options);
}

/**
 * Fetches the URL with a HTTP POST request
 *
 * @api
 * @param string $url
 * @param array $options Ring Request
 */
function post($url, array $options = [])
{
    $options['http_method'] = 'POST';
    return fetch($url, $options);
}

/**
 * Fetches the URL with a HTTP PUT request
 *
 * @api
 * @param string $url
 * @param array $options Ring Request
 */
function put($url, array $options = [])
{
    $options['http_method'] = 'PUT';
    return fetch($url, $options);
}

/**
 * Fetches the URL with a HTTP DELETE request
 *
 * @api
 * @param string $url
 * @param array $options Ring Request
 */
function delete($url, array $options = [])
{
    $options['http_method'] = 'DELETE';
    return fetch($url, $options);
}

/**
 * Fetches the URL with a HTTP OPTIONS request
 *
 * @api
 * @param string $url
 * @param array $options Ring Request
 */
function options($url, array $options = [])
{
    $options['http_method'] = 'OPTIONS';
    return fetch($url, $options);
}

/**
 * Fetches the URL with a HTTP HEAD request
 *
 * @api
 * @param string $url
 * @param array $options Ring Request
 */
function head($url, array $options = [])
{
    $options['http_method'] = 'HEAD';
    return fetch($url, $options);
}
