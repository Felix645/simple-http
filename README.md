# simple-http

## Installtion

```
composer require neon-php/simple-http
```

## Introduction

**simple-http** provides a small interface to create really simple
http-requests using the Guzzle-HTTP-Client.

## Usage

### Making Requests

The easiest way to make a new request is by using the static
facade class *Neon\Http\Facade\Http*. This class provides static methods
to create a new request instance of the class *Neon\Http\Http*.

In the simplest way you can make a request by calling a method for the
corresponding http verb:

```php
<?php

use Neon\Http\Facade\Http;

$response = Http::get('http://example.com/api');
```

The following methods and their corresponding http verbs are supported:

```php
<?php

use Neon\Http\Facade\Http;

$response = Http::get('http://example.com/api');
$response = Http::post('http://example.com/api');
$response = Http::put('http://example.com/api');
$response = Http::patch('http://example.com/api');
$response = Http::delete('http://example.com/api');
```

#### Setting base url

By using the method *setBaseURL* you can set a base url that will be used
for all following requests:

```php
<?php

use Neon\Http\Facade\Http;

Http::setBaseURL('http://example.com');

$response = Http::get('/api');
```

#### Framework request methods

Some frameworks may need a special request parameter to be sent to determine the request method.
For example, sometimes a **put**-request is basically a **post**-request which contains to following
request parameter.

```
_method: put
```

To shorten this process the use of framework methods the corresponding functionality
can be activated by using the method *setFrameworkMethod*:

```php
<?php

use Neon\Http\Facade\Http;

Http::setBaseURL('http://example.com');
Http::setFrameworkMethod(true);

$response = Http::put('/api');
```

#### Request inputs

To add input value to your request (post, put, patch, delete) you can call the
*addParam* method to provide key/value pairs **before** calling the request method:

```php
<?php

use Neon\Http\Facade\Http;

$response = Http::addParam('key', 'value')->post('/api');
```

Alternatively you can add an array as the second parameter of the request method to provide
multiple key/value pairs:

```php
<?php

use Neon\Http\Facade\Http;

$response = Http::post('/api', [
    'some_key' => 'some_value',
    'another_key' => 'another_value'
]);
```

To provide query parameters for GET requests, you need to provide the *query*-key to the
input array:

```php
<?php

use Neon\Http\Facade\Http;

$response = Http::post('/api', [
    'query' => [
        'some_key' => 'some_value',
        'another_key' => 'another_value'
    ]
]);

// Resulting request url:
// http://example.com/api?some_key=some_value&another_key=another_value
```

#### Adding request headers

To add additional request headers you can call the *addHeader* method **before** calling
the request method:

```php
<?php

use Neon\Http\Facade\Http;

$response = Http::addHeader('Accept', 'application/json')->post('/api');
```

To quickly add a bearer token to the authorization header you can use the *bearer* method:

```php
<?php

use Neon\Http\Facade\Http;

$response = Http::bearer($token)->post('/api');
```

#### Adding files

Files can be added via the *file* method. Just provide a key, the file name and the file location:

```php
<?php

use Neon\Http\Facade\Http;

$image = $_FILES['image'];

$response = Http::file('image', $image['name', $image['tmp_name']])->post('/api');
```


### Response Handling

After making a request, the corresponding method will return an instance of *Neon\Http\Response*.
This object provides several methods to work with the response.

#### Headers

The method *hasHeader* checks if a provided header key exists in the response:

```php
<?php

use Neon\Http\Facade\Http;

$response = Http::get('http://example.com/api');

if ($response->hasHeader('Content-Length')) {
    // Header Content-Length exists
}
```

The method *getHeader* returns the header values of the given header key. The values
are returned as an array of strings:

```php
<?php

$values = $response->getHeader('Content-Length');
```

#### Status Code

The method *code* returns the http status code of the response:

```php
<?php

$code = $response->code();
```

Additionally, there are several methods to check for specific pre defines status codes:

```php
<?php

// Status code is 200, 201 or 204
$response->successfull();

// Status code is bigger than or equal to 400
$response->failure();

// Status code is bigger than or equal to 500
$response->serverError();

// Status code is bigger than or equal to 400 AND less than 500
$response->clientError();

// Status code is 201
$response->created();

// Status code is 204
$response->noContent();

// Status code is 404
$response->notFound();

// Status code is 401
$response->unauthorized();

// Status code is 403
$response->forbidden();

// Status code is 400
$response->badRequest();
```

#### Response body

The method *body* returns the response body as a string:

```php
<?php

$body = $response->body();
```

The method *bodyRaw* returns the response body as an instance of *Psr\Http\Message\StreamInterface*:

```php
<?php

$body_stream = $response->bodyRaw();
```

The method *json* converts the body string to a php array and returns it. The method throws an
*Neon\Http\Exceptions\RequestException* if the body is not a valid json string.

```php
<?php

use Neon\Http\Exceptions\RequestException;

try {
    $body = $response->json();
} catch (RequestException $e) {
    // Response body is not json
}
```

### Error Handling

The request methods *get*, *post*, *put*, *patch* and *delete* each throw an *Neon\Http\Exceptions\RequestException* 
if the request was, for whatever reason, unsuccessfull.