<?php 

namespace Desa\Router;

/**
 *
 *
 *
 *
 *
 */
interface RouteInterface
{
    
    /**
     * Return the http method associated with route
     * 
     * @return string
     */
    public function getMethods();
    
    /**
     * Return the callback that should execute if route is matched
     * 
     * @return Callable|string|array
     */
    public function getCallback();
    
    /**
     * Return the parameter names associated with path of route
     * 
     * @return array
     */
    public function getParameters();
    
    /**
     * Return the regex pattern that a request uri should match against
     * 
     * 
     * @return string
     */
    public function getRegexPattern();
}