# httpfetch

[![Build Status](https://travis-ci.org/CHH/httpfetch.svg)](https://travis-ci.org/CHH/httpfetch)
[![Latest Stable Version](https://poser.pugx.org/chh/httpfetch/v/stable)](https://packagist.org/packages/chh/httpfetch)

httpfetch provides a simple function `fetch` to make HTTP requests in small scripts easy and fast.

httpfetch relies heavily on [RingPHP](https://github.com/guzzle/RingPHP) for its robust low level HTTP abstraction and for the ability to make asynchronous requests.

## Use Cases

* As replacement for `file_get_contents`, but with async support and proper handling of the certificates used in HTTPS.
* `file_get_contents` or cURL are notoriously insecure without proper configuration, which is hard to do. Also see this [secure file_get_contents wrapper](https://github.com/padraic/file_get_contents).
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

[ring request]: http://ringphp.readthedocs.org/en/latest/spec.html#requests
[ring response]: http://ringphp.readthedocs.org/en/latest/spec.html#responses

Makes a HTTP request to the provided URL. The options follow the [RingPHP specification for requests][ring request] and the returned response follows the [RingPHP specification for responses][ring response].

All requests are made asynchronously by default, when supported by the handler. This can be turned off by setting the `future` option to `false`.

httpfetch implements a few additional options for convenience:

* `follow_location` (default: `true`): Follows responses which return a "Location" header
* `max_redirects` (default: `10`): Number of redirects to follow
* `auth` (default: `null`): Username/password pair as array for Basic Authentication, e.g. `["user", "password"]`

For example a POST request by using the `http_method` parameter:

```php
$response = fetch('http://www.example.com', [
    'http_method' => 'POST',
    'body' => 'foo'
]);

var_dump($response['status']);
var_dump(stream_get_contents($response['body']));
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
