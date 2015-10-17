<?php 

namespace Desa\Router;

/**
 *
 *
 *
 *
 *
 */
interface RouteCollectionInterface
{
    /**
     * 
     * 
     * @param <type> $name 
     * 
     * @return <type>
     */
    public function getRoute($name);
    /**
     * 
     * 
     * @param <type> $method 
     * @param <type> $path 
     * @param <type> $callback 
     * @param <type> $requirments  
     * 
     * @return <type>
     */
    public function addRoute($name, $method, $path, callable $callback, array $requirments = null);
    
    /**
     * 
     * 
     * @param <type> $name 
     * 
     * @return <type>
     */
    public function removeRoute($name);
    
    /**
     * 
     * 
     * @param <type> $name 
     * @param <type> $method 
     * @param <type> $path 
     * @param <type> $callback 
     * @param <type> $requirments 
     * 
     * @return <type>
     */
    public function except($name, $methods, $path, Callable $callback, $requirments = null);
    
    /**
     * 
     * 
     * @param <type> $name 
     * @param <type> $path 
     * @param <type> $callback 
     * @param <type> $requirments 
     * 
     * @return <type>
     */
    public function any($name, $path, Callable $callback, $requirments = null);

    /**
     *
     *
     * @param string                $name        The name of the route
     * @param string                $path        The path which the route should match
     * @param Callable|string|array $callback    Callback function to execute if route is matched
     * @param array                 $requirments Requirments (regex patterns) for each path parameter
     *
     * @return Desa\Router\Route
     */
    public function get($name, $path, Callable $callback, $requirments = null);
    
    /**
     *
     *
     * @param string                $name        The name of the route
     * @param string                $path        The path which the route should match
     * @param Callable|string|array $callback    Callback function to execute if route is matched
     * @param array                 $requirments Requirments (regex patterns) for each path parameter
     *
     * @return Desa\Router\Route
     */
    public function post($name, $path, Callable $callback, $requirments = null);
    
    /**
     *
     *
     * @param string                $name        The name of the route
     * @param string                $path        The path which the route should match
     * @param Callable|string|array $callback    Callback function to execute if route is matched
     * @param array                 $requirments Requirments (regex patterns) for each path parameter
     *
     * @return Desa\Router\Route
     */
    public function put($name, $path, Callable $callback, $requirments = null);
    
    /**
     *
     *
     * @param string                $name        The name of the route
     * @param string                $path        The path which the route should match
     * @param Callable|string|array $callback    Callback function to execute if route is matched
     * @param array                 $requirments Requirments (regex patterns) for each path parameter
     *
     * @return Desa\Router\Route
     */
    public function patch($name, $path, Callable $callback, $requirments = null);
    
    /**
     *
     *
     * @param string                $name        The name of the route
     * @param string                $path        The path which the route should match
     * @param Callable|string|array $callback    Callback function to execute if route is matched
     * @param array                 $requirments Requirments (regex patterns) for each path parameter
     *
     * @return Desa\Router\Route
     */
    public function delete($name, $path, Callable $callback, $requirments = null);
}