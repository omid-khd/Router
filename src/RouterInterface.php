<?php 

namespace Desa\Router;

use Symfony\Component\HttpFoundation\Request;

interface RouterInterface
{
    /**
     * Return the route that is matched against http request if any route is matched and
     * false if nothing matched.
     * 
     * @param Request $request Http request object
     * 
     * @return Route|bool
     */
    public function match(Request $request);
}