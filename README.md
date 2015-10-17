# Router
A package for routing http requests

##usage
<<<<<<< HEAD

=======
>>>>>>> origin/master
```php
require 'path/to/vendor/autoload.php';

use Desa\Router\Router;
use Symfony\Component\HttpFoundation\Request;

$router = new Router

$router->addRoute(
	'name',
	'GET|POST|PUT|PATH|DELETE',
	'/path/{variable}/anotherPath/{anotherVar}',
	$callback,
	$requirments
);

$request = Request::createFromGlobals();

$matchedRoute = $router->match($request);
```
<<<<<<< HEAD

other methods for adding a route:

```php
$router->get('name', '/path/{var}', $callback, $requirments);

$router->post('name', '/path/{var}', $callback, $requirments);

$router->put('name', '/path/{var}', $callback, $requirments);

$router->patch('name', '/path/{var}', $callback, $requirments);

$router->delete('name', '/path/{var}', $callback, $requirments);

$router->any('name', '/path/{var}', $callback, $requirments);

$router->except('name', 'PUT|PATH|DELETE', '/path/{var}', $callback, $requirments);
```

also you colud customize the `{var}` part of the path by passing an associative array as the last parameter of top methods:

```php
$router->get(
	'user',
	'users/{user}/profile/{section}',
	$callback,
	['user' => '\d+', 'section' => '[a-zA-Z0-9_-]']
);
```
=======
>>>>>>> origin/master
