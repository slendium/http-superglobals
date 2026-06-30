# Slendium HTTP-Superglobals

Implementation of the [slendium/http](https://git.frisiapp.com/slendium/http) library.

Incoming requests are based on the PHP superglobals (`$_GET`, `$_POST`, `$_FILES`, `$_COOKIE` and `$_SERVER`)
and responses can be printed to the output buffer (using `header()` and `echo`).

## Installation

Simply run `composer require slendium/http-superglobals` to add it to your project.

## Usage

From your PHP script, simply create a new instance of the `GlobalRequest` object and use it wherever
a `Slendium\Http\Request` is expected.

```php
// instance wrapped in networking information, returns a Networked<Request>
$request = GlobalRequest::createNetworkedInstance();

// regular instance
$request = new GlobalRequest;

// use the request
$response = MyFramework::respond($request);

// output the response
GlobalResponse::output($response);
```
