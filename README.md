# KoolRouter
KoolRouter is a fast, easy to integrate Router. It's inspired by by [klein.php](https://github.com/chriso/klein.php/)
and [AltoRouter] (https://github.com/dannyvankooten/AltoRouter).

It should me used in conjunction with a dispatcher and HTTP interface, as it does (by purpose) not 
provides these features.

## Key features
* Routing URL's to callback functions
* Reverse routing a route and parameters to the corresponding path
* Simple routing syntax

## Starting with KoolRouter
1. Make sure PHP 5.3.* is installed
2. Install KoolRouter using Composer or manually
3. Make KoolRouter handle your requests (for example trough URL Rewriting)

## Supported Routes
You can use the following syntax within your routes. 
```
    *                    // Match all request URIs
    [i]                  // Match an integer
    [i:id]               // Match an integer as 'id'
    [a:action]           // Match alphanumeric characters as 'action'
    [h:key]              // Match hexadecimal characters as 'key'
    [:action]            // Match anything up to the next / or end of the URI as 'action'
    [create|edit:action] // Match either 'create' or 'edit' as 'action'
    [*]                  // Catch all (lazy, stops at the next trailing slash)
    [*:trailing]         // Catch all as 'trailing' (lazy)
    [**:trailing]        // Catch all (possessive - will match the rest of the URI)
    .[:format]?          // Match an optional parameter 'format' - a / or . before the block is also optional

Some examples

    /pages[/index]?            // Matches "/pages" and "/pages/index"
    /posts/[*:title][i:id]     // Matches "/posts/this-is-a-title-123"
    /output.[xml|json:format]? // Matches "/output", "output.xml", "output.json"
    /[:controller]?/[:action]? // Matches the typical /controller/action format

```

## Example
```php

use ElzeKool\KoolRouter\Router;

$router = new Router();

// Setup route
$router->map(
    'GET /pages/[i:id]', 
    function($method, $path, $parameters, $extra) { 
        echo 'Hello i\'m page ' . $parameters['id'];
    },
    'page_view'
);

// Start routing
$router->run();


// Reverse routing
// (returns: /pages/10)
$route = $router->reverse('page_view', array(
    'id' => 10
));

```


## License

(MIT License)

Copyright (c) 2013 Elze Kool <info@kooldevelopment.nl>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
