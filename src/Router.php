<?php

namespace Desa\Router;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Desa\Router\Exceptions\RouteNotFoundException;

class Router implements RouterInterface, RouteCollectionInterface
{
    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * Router constructor.
     *
     * @param RouteCollectionInterface|null $routesCollection
     */
    public function __construct(RouteCollectionInterface $routesCollection = null)
    {
        $this->routes = $routesCollection ?: new RouteCollection;
    }

    /**
     *{@inheritdoc}
     */
    public function match(Request $request)
    {
        $url     = $request->getRequestUri();
        $method  = $request->getMethod();
        $pattern = $this->getRoutesRegexPattern($method);

        if (preg_match($pattern, $url, $matches)) {
            // beacuse pattern is composed of all routes patterns we must find the matched
            // route somehow. matched route is the first entry in the matches array that
            // has string key and has non empty value. so we iterate on matches and if
            // entry has string key and has non empty value we return it as matched route.
            foreach ($matches as $key => $value) {
                if (is_string($key) && '' !== $value) {
                    return $this->getRoute($key);
                }
            }
        } else {
            $msg = sprintf('There is no matching route for "%s"', $url);
            throw new RouteNotFoundException($msg);
        }
    }

    /**
     * @param $method
     *
     * @return string
     */
    protected function getRoutesRegexPattern($method)
    {
        $method = strtoupper($method);
        $pattern = '';

        foreach ($this->routes as $name => $route) {
            // only routes that have $method in their methods are allowed
            if (in_array($method, $route->getMethods(), true)) {
                $pattern .= sprintf('(?P<%s>%s)|', $name, $route->getRegexPattern());
            }
        }

        return '`'.trim($pattern, '|').'`u';
    }

    /**
     *{@inheritdoc}
     */
    public function getRoute($name)
    {
        return $this->routes->getRoute($name);
    }

    /**
     *{@inheritdoc}
     */
    public function addRoute($name, $method, $path, callable $callback, array $requirements = null)
    {
        $this->routes->addRoute($name, $method, $path, $callback, $requirements);
    }

    /**
     *{@inheritdoc}
     */
    public function removeRoute($name)
    {
        $this->routes->remove($name);
    }

    /**
     *{@inheritdoc}
     */
    public function any($name, $path, Callable $callback, $requirements = null)
    {
        $this->routes->any($name, $path, $callback, $requirements);
    }

    /**
     *{@inheritdoc}
     */
    public function except($name, $methods, $path, Callable $callback, $requirements = null)
    {
        $this->route->except($name, $methods, $path, $callback, $requirements);
    }

    /**
     *{@inheritdoc}
     */
    public function get($name, $path, Callable $callback, $requirements = null)
    {
        $this->routes->get($name, $path, $callback, $requirements);
    }

    /**
     *{@inheritdoc}
     */
    public function post($name, $path, Callable $callback, $requirements = null)
    {
        $this->routes->post($name, $path, $callback, $requirements);
    }

    /**
     *{@inheritdoc}
     */
    public function put($name, $path, Callable $callback, $requirements = null)
    {
        $this->routes->put($name, $path, $callback, $requirements);
    }

    /**
     *{@inheritdoc}
     */
    public function patch($name, $path, Callable $callback, $requirements = null)
    {
        $this->routes->patch($name, $path, $callback, $requirements);
    }

    /**
     *{@inheritdoc}
     */
    public function delete($name, $path, Callable $callback, $requirements = null)
    {
        $this->routes->delete($name, $path, $callback, $requirements);
    }
}