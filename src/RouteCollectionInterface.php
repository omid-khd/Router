<?php 

namespace Desa\Router;

interface RouteCollectionInterface
{
    /**
     * Get a route by it's name
     *
     * @param $name
     *
     * @return Route
     */
    public function getRoute($name);

    /**
     * Add a route
     *
     * @param            $name         The name of the route
     * @param            $method       Http method
     * @param            $path         The path which the route should match
     * @param callable   $callback     Callback function to execute if route is matched
     * @param array|null $requirements Requirements (regex patterns) for each path parameter
     *
     * @return void
     */
    public function addRoute($name, $method, $path, callable $callback, array $requirements = null);

    /**
     * Remove a route by it's name
     *
     * @param string $name Name of the route to remove
     *
     * @return void
     */
    public function removeRoute($name);

    /**
     * Add a route for all http methods except given methods
     *
     * @param          $name         The name of the route
     * @param          $methods      Http method
     * @param          $path         The path which the route should match
     * @param callable $callback     Callback function to execute if route is matched
     * @param null     $requirements Requirements (regex patterns) for each path parameter
     *
     * @return void
     */
    public function except($name, $methods, $path, Callable $callback, $requirements = null);

    /**
     * Add a route for all http methods
     *
     * @param string   $name         The name of the route
     * @param string   $path         The path which the route should match
     * @param Callable $callback     Callback function to execute if route is matched
     * @param array    $requirements Requirements (regex patterns) for each path parameter
     *
     * @return void
     */
    public function any($name, $path, Callable $callback, $requirements = null);

    /**
     * Add a route for GET http method
     *
     * @param string                $name         The name of the route
     * @param string                $path         The path which the route should match
     * @param Callable|string|array $callback     Callback function to execute if route is matched
     * @param array                 $requirements Requirements (regex patterns) for each path parameter
     *
     * @return void
     */
    public function get($name, $path, Callable $callback, $requirements = null);
    
    /**
     * Add a route for POST http method
     *
     * @param string   $name         The name of the route
     * @param string   $path         The path which the route should match
     * @param Callable $callback     Callback function to execute if route is matched
     * @param array    $requirements Requirements (regex patterns) for each path parameter
     *
     * @return void
     */
    public function post($name, $path, Callable $callback, $requirements = null);

    /**
     * Add a route for PUT http method
     *
     * @param string   $name         The name of the route
     * @param string   $path         The path which the route should match
     * @param Callable $callback     Callback function to execute if route is matched
     * @param array    $requirements Requirements (regex patterns) for each path parameter
     *
     * @return void
     */
    public function put($name, $path, Callable $callback, $requirements = null);

    /**
     * Add a route for PATCH http method
     *
     * @param string   $name         The name of the route
     * @param string   $path         The path which the route should match
     * @param Callable $callback     Callback function to execute if route is matched
     * @param array    $requirements Requirements (regex patterns) for each path parameter
     *
     * @return void
     */
    public function patch($name, $path, Callable $callback, $requirements = null);

    /**
     * Add a route for DELETE http method
     *
     * @param string   $name         The name of the route
     * @param string   $path         The path which the route should match
     * @param Callable $callback     Callback function to execute if route is matched
     * @param array    $requirements Requirements (regex patterns) for each path parameter
     *
     * @return void
     */
    public function delete($name, $path, Callable $callback, $requirements = null);
}