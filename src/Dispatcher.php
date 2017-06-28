<?php

namespace Desa\Router;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Desa\Router\Exceptions\RouteNotFoundException;

class Dispatcher
{
    /**
     * @var Router;
     */
    protected $router;

    /**
     * Dispatcher constructor.
     *
     * @param RouterInterface|null $router
     */
    public function __construct(RouterInterface $router = null)
    {
        $this->router = $router ?: new Router;
    }

    /**
     * Dispatch request to router and return a response to client
     *
     * @param Request $request
     */
    public function dispatch(Request $request)
    {
        try {
            $route = $this->router->match($request);

            if (false === $route->isStatic()) {
                $params = $this->getRouteData($route, $request->getUri());

                $request->attributes->replace($params);
            }

            $response = call_user_func($route->getCallback(), $request, new Response);

            if (!$response instanceof Response) {
                $response = new Response('Not Found', Response::HTTP_NOT_FOUND);
            }
        } catch (RouteNotFoundException $e) {
            $response = new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            $response = new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        $response->send();
    }

    /**
     * Extracts route parameters values from request uri
     *
     * @param Route  $route
     * @param string $uri
     *
     * @return array
     */
    protected function getRouteData(Route $route, $uri)
    {
        $pattern = '`'.$route->getRegexPattern().'`';

        preg_match_all($pattern, $uri, $matches, PREG_SET_ORDER);

        // first entry of $matches is entire match, that is, it is $route->path. we dont need that.
        // other entries are matched parameters so we shift first entry of $matches.
        array_shift($matches[0]);

        $params = $matches[0];
        $routeParams = $route->getParameters();

        if (count($params) < count($routeParams)) {
            $params = array_pad($params, (count($routeParams) - count($params)), null);
        }

        return array_combine($routeParams, $params);
    }
}