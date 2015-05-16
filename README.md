# httpfetch

[![Build Status](https://travis-ci.org/CHH/httpfetch.svg)](https://travis-ci.org/CHH/httpfetch)
[![Latest Stable Version](https://poser.pugx.org/chh/httpfetch/v/stable)](https://packagist.org/packages/chh/httpfetch)

httpfetch provides a simple function `fetch` to make HTTP requests in small scripts easy and fast.

httpfetch relies heavily on [RingPHP](https://github.com/guzzle/RingPHP) for its robust low level HTTP abstraction and for the ability to make asynchronous requests.

## Use Cases

* As replacement for `file_get_contents`, but with async support, proper support for all HTTP features, easy passing of request headers, parsing of response headers, and proper handling of the certificates used by HTTPS.
* `file_get_contents` is notoriously insecure when used with HTTPS without proper configuration, which is hard to do. Also see this [secure file_get_contents wrapper](https://github.com/padraic/file_get_contents).
* Making simple web service clients without depending on Guzzle.

## Install

Via Composer

``` bash
$ composer require chh/httpfetch
```

## Usage

``` php
use function chh\httpfetch\fetch;

$response = fetch("https://www.example.com");

echo stream_get_contents($response['body']);
```

## The chh\httpfetch Namespace

### fetch($url, array $options = [])

[ring specification]: http://ringphp.readthedocs.org/en/latest/spec.html
[ring request]: http://ringphp.readthedocs.org/en/latest/spec.html#requests
[ring response]: http://ringphp.readthedocs.org/en/latest/spec.html#responses

Makes a HTTP request to the provided URL. The options follow the [RingPHP specification for requests][ring request] and the returned response follows the [RingPHP specification for responses][ring response].

All requests are made asynchronously by default, when supported by the handler. This can be turned off by setting the `future` option to `false`.

httpfetch implements a few additional options for convenience:

* `follow_location` (default: `true`): Follows responses which return a "Location" header
* `max_redirects` (default: `10`): Number of redirects to follow
* `auth` (default: `null`): Username/password pair as array for Basic Authentication, e.g. `["user", "password"]`

Responses are array-like objects with the following keys:

* `body`: (string, fopen resource, Iterator, GuzzleHttp\Stream\StreamInterface) The body of the response, if present. Can be a string, resource returned from fopen, an Iterator that yields chunks of data, an object that implemented \_\_toString, or a GuzzleHttp\Stream\StreamInterface.
* `effective_url`: (string) The URL that returned the resulting response.
* `error`: (\Exception) Contains an exception describing any errors that were encountered during the transfer.
* `headers`: (Required, array) Associative array of headers. Each key represents the header name. Each value contains an array of strings where each entry of the array is a header line. The headers array MAY be an empty array in the event an error occurred before a response was received.
* `reason`
(string) Optional reason phrase. This option should be provided when the reason phrase does not match the typical reason phrase associated with the status code. See RFC 7231 for a list of HTTP reason phrases mapped to status codes.
* `status`: (Required, integer) The HTTP status code. The status code MAY be set to null in the event an error occurred before a response was received (e.g., a networking error).
* `transfer_stats`: (array) Provides an associative array of arbitrary transfer statistics if provided by the underlying handler.
* `version`: (string) HTTP protocol version. Defaults to 1.1.

For example a POST request by using the `http_method` parameter:

```php
$response = fetch('http://www.example.com', [
    'http_method' => 'POST',
    'body' => 'foo'
]);

var_dump($response['status']);
var_dump(stream_get_contents($response['body']));
```

Example: Doing an async GET request with the Promise API:

```php
fetch('http://www.example.com')->then(function ($response) {
  // Save the response stream:
  $out = fopen('/tmp/foo.txt', 'w+b');
  stream_copy_to_stream($response['body'], $out);
  fclose($out);
});
```

Example: Doing an async request with the Future API:

```php
$response = fetch('http://www.example.com');

// Do some other stuff

$response->wait();

echo stream_get_contents($response['body']);
```

Example: Doing requests in parallel:

```php
$response1 = fetch('http://www.example.com');
$response2 = fetch('http://www.foo.com');

echo $response1['status'], "\n";
echo $response2['status'], "\n";
```

### get(), post(), put(), delete(), head(), options()

Helper methods for common HTTP methods. They all follow the same signature of `($url, array $options = [])`.

Example:

```php
use chh\httpfetch;

// GET request
$response = httpfetch\get("https://www.example.com");

// POST request with body
$response = httpfetch\post("https://www.example.com", [
    'body' => 'foo',
]);
```

### set_default_handler(callable $handler)

Overrides the Guzzle Ring Client handler which is used by the `fetch` function. Handlers are callables which follow the [RingPHP specification][].

Example: Force the usage of PHP's http:// stream wrapper:

```php
chh\httpfetch\set_default_handler(new Guzzle\Ring\Client\StreamHandler);

fetch('http://example.com');
```

## Testing

``` bash
$ make test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email me@christophh.net instead of using the issue tracker.

## Credits

- [Christoph Hochstrasser](https://github.com/CHH)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
