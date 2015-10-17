<?php

namespace Desa\Router;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Desa\Router\Exceptions\RouteNotFoundException;

class Dispatcher
{
    /**
     * @var Desa\Router\Router;
     */
    protected $router;

    /**
     *
     *
     * @param <type> $router
     *
     * @return <type>
     */
    public function __construct(RouterInterface $router = null)
    {
        $this->router = $router ?: new Router;
    }

    /**
     *
     *
     * @param <type> $request
     *
     * @return <type>
     */
    public function dispatch(Request $request)
    {
        try {
            $route = $this->router->match($request);

            if (! $route->isStatic()) {
                $params = $this->getRouteData($route, $request);
                $request->attributes->add($params);
            }

            $response = call_user_func($route->getCallback(), $request, new Response);

            if (!$response instanceof Response) {
                $response = new Response('Not Found', Response::HTTP_NOT_FOUND);
            }
        } catch (RouteNotFoundException $e) {
            $response = new Response($e->getMessage(), 404);
        } catch (\Exception $e) {
            $response = new Response($e->getMessage(), 404);
        }

        $response->send();
    }

    /**
     *
     *
     * @param <type> $route
     * @param <type> $request
     *
     * @return <type>
     */
    protected function getRouteData(Route $route, Request $request)
    {
        $uri = $request->getRequestUri();
        $pattern = '`'.$route->getRegexPattern().'`';

        preg_match_all($pattern, $uri, $matches, PREG_SET_ORDER);

        // first entry of $matches is entire match, that is, it is $route->path. we dont need that.
        // other entries are matched parameters so we shift first entry of $matches.
        array_shift($matches[0]);

        $params = array_map('urldecode', $matches[0]);
        $routeParams = $route->getParameters();

        if (count($params) < count($routeParams)) {
            $count = count($routeParams) - count($params);
            for ($i = 0; $i < $count; $i++) {
                $params[] = null;
            }
        }

        return array_combine($routeParams, $params);
    }
}