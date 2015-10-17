<?php

namespace Desa\Router;

/**
 *
 *
 *
 *
 *
 */
class RouteCollection implements \IteratorAggregate, \Countable
{
    protected $httpMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
    /**
     * @var array
     */
    protected $routes = [];

    /**
     * Return a route by its name
     *
     * @param string $key
     *
     * @return Desa\Router\Route|null
     */
    public function getRoute($key)
    {
        if (isset($this->routes[$key])) {
            return $this->routes[$key];
        }
    }

    /**
     * 
     * 
     * @param <type> $methods 
     * 
     * @return <type>
     */
    protected function getMethods($methods)
    {
        if (!is_string($methods) && !is_array($methods)) {
            $msg = sprintf(
                'Only array and string are allowed for http method. given input type is %s .',
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
     * 
     * 
     * @param <type> $methods 
     * 
     * @return <type>
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
    public function addRoute($name, $methods, $path, callable $callback, array $requirments = null)
    {
        $args = [
            'methods' => $this->getMethods($methods),
            'path' => $path,
            'callback' => $callback,
            'requirments' => $requirments
        ];

        $this->routes[$name] = new Route($args);
    }

    /**
     *{@inheritdoc}
     */
    public function removeRoute($name)
    {
        if (array_key_exists($name, $this->routes)) {
            unset($this->routes[$name]);
        }
    }
    
    /**
     *{@inheritdoc}
     */
    public function any($name, $path, Callable $callback, $requirments = null)
    {
        $this->addRoute($name, $this->httpMethods, $path, $callback, $requirments);
    }
    
    /**
     *{@inheritdoc}
     */
    public function except($name, $methods, $path, Callable $callback, $requirments = null)
    {
        $methods = array_diff($this->httpMethods, $this->getMethods($methods));
        
        $this->addRoute($name, $methods, $path, $callback, $requirments);
    }

    /**
     *{@inheritdoc}
     */
    public function get($name, $path, $callback, $requirments = null)
    {
        $this->addRoute($name, 'GET', $path, $callback, $requirments);
    }

    /**
     *{@inheritdoc}
     */
    public function post($name, $path, $callback, $requirments = null)
    {
        $this->addRoute($name, 'POST', $path, $callback, $requirments);
    }

    /**
     *{@inheritdoc}
     */
    public function put($name, $path, $callback, $requirments = null)
    {
        $this->addRoute($name, 'PUT', $path, $callback, $requirments);
    }

    /**
     *{@inheritdoc}
     */
    public function patch($name, $path, $callback, $requirments = null)
    {
        $this->addRoute($name, 'PATCH', $path, $callback, $requirments);
    }

    /**
     *{@inheritdoc}
     */
    public function delete($name, $path, $callback, $requirments = null)
    {
        $this->addRoute($name, 'DELETE', $path, $callback, $requirments);
    }

    /**
     *
     *
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
    }

    /**
     *
     *
     *
     * @return int
     */
    public function count()
    {
        return count($this->routes);
    }
}