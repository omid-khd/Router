# Router
A package for routing http requests

##usage
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
