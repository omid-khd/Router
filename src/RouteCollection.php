<?php

namespace Desa\Router;

class RouteCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var array
     */
    protected $routes;

    /**
     * @var array
     */
    protected $httpMethods;

    /**
     * RouteCollection constructor.
     */
    public function __construct()
    {
        $this->routes = [];
        $this->httpMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
    }

    /**
     * Return a route by its name
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getRoute($name)
    {
        if (isset($this->routes[$name])) {
            return $this->routes[$name];
        }
    }

    /**
     * @param $methods
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function getMethods($methods)
    {
        if (!is_string($methods) && !is_array($methods)) {
            $msg = sprintf(
                'Only array and string are allowed for http method. given input type is "%s" .',
                gettype($methods)
            );
            throw new \InvalidArgumentException($msg);
        }
        
        if (is_string($methods)) {
            $methods = explode('|', $methods);
        }

        $this->validateMethods($methods);

        return $methods;
    }

    /**
     * @param array $methods
     */
    protected function validateMethods(array $methods)
    {
        foreach ($methods as $method) {
            if (!in_array(strtoupper($method), $this->httpMethods, true)) {
                $msg = sprintf('Method %s is not allowed.', strtoupper($method));
                throw new BadMethodException($msg);
            }
        }
    }

    /**
     *{@inheritdoc}
     */
    public function addRoute($name, $methods, $path, callable $callback, array $requirements = null)
    {
        $args = [
            'methods'      => $this->getMethods($methods),
            'path'         => $path,
            'callback'     => $callback,
            'requirements' => $requirements
        ];

        $this->routes[$name] = new Route($args);
    }

    /**
     *{@inheritdoc}
     */
    public function removeRoute($name)
    {
        if (isset($this->routes[$name])) {
            unset($this->routes[$name]);
        }
    }
    
    /**
     *{@inheritdoc}
     */
    public function any($name, $path, Callable $callback, $requirements = null)
    {
        $this->addRoute($name, $this->httpMethods, $path, $callback, $requirements);
    }
    
    /**
     *{@inheritdoc}
     */
    public function except($name, $methods, $path, Callable $callback, $requirements = null)
    {
        $methods = array_diff($this->httpMethods, $this->getMethods($methods));
        
        $this->addRoute($name, $methods, $path, $callback, $requirements);
    }

    /**
     *{@inheritdoc}
     */
    public function get($name, $path, $callback, $requirements = null)
    {
        $this->addRoute($name, 'GET', $path, $callback, $requirements);
    }

    /**
     *{@inheritdoc}
     */
    public function post($name, $path, $callback, $requirements = null)
    {
        $this->addRoute($name, 'POST', $path, $callback, $requirements);
    }

    /**
     *{@inheritdoc}
     */
    public function put($name, $path, $callback, $requirements = null)
    {
        $this->addRoute($name, 'PUT', $path, $callback, $requirements);
    }

    /**
     *{@inheritdoc}
     */
    public function patch($name, $path, $callback, $requirements = null)
    {
        $this->addRoute($name, 'PATCH', $path, $callback, $requirements);
    }

    /**
     *{@inheritdoc}
     */
    public function delete($name, $path, $callback, $requirements = null)
    {
        $this->addRoute($name, 'DELETE', $path, $callback, $requirements);
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->routes);
    }
}