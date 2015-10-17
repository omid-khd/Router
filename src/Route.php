<?php

namespace Desa\Router;

use InvalidArgumentException;
use Desa\Router\RouteInterface;
use Desa\Router\Exceptions\BadMethodException;

/**
 *
 *
 *
 *
 *
 */
class Route implements RouteInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $methods = [];

    /**
     * @var Callable
     */
    protected $callback;

    /**
     * @var string
     */
    protected $regexPattern;

    /**
     * @var array
     */
    protected $routeParameters = [];

    /**
     * @var string
     */
    protected $optionalRouteParameter;

    /**
     * @var array
     */
    protected $parametersCondition = [];

    /**
     *
     *
     * @param array $arguments
     *
     * @return void
     */
    public function __construct(array $arguments)
    {
        $path = $arguments['path'];
        $callback = $arguments['callback'];
        $methods = isset($arguments['methods']) ? $arguments['methods'] : ['GET'];
        $requirments = isset($arguments['requirments']) ? $arguments['requirments'] : null;

        $this->setMethods($methods);
        $this->setPath($path);
        $this->setCallback($callback);
        $this->extractParameters($path);

        if ($requirments) {
            $this->setRequirments($requirments);
        }

        $this->compileRoute();
    }

    /**
     *
     *
     * @param string $methods
     *
     * @return $this
     */
    protected function setMethods(array $methods)
    {
        foreach ($methods as $method) {
            if (!in_array(strtoupper($method), ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], true)) {
                $msg = sprintf('Method %s is not allowed.', strtoupper($method));
                throw new BadMethodException($msg);
            }
        }

        $this->methods = $methods;
    }

    /**
     *{@inheritdoc}
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     *
     *
     * @param string $path
     *
     * @return $this
     */
    protected function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Return the path of the route
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     *
     *
     * @param Callable $callback
     *
     * @return $this
     */
    protected function setCallback(Callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     *{@inheritdoc}
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     *
     *
     *
     * @return void
     */
    protected function compileRoute()
    {
        $pathStaticParts = preg_split('`{\w+}`', $this->path);
        $pathStaticParts = array_map('preg_quote', $pathStaticParts);

        $routeRegexPattern = $this->buildRegexPattern($pathStaticParts);

        $this->setRegexPattern($routeRegexPattern);
    }

    /**
     *
     *
     * @param array $pathStaticParts
     *
     * @return string
     */
    protected function buildRegexPattern($pathStaticParts)
    {
        $pattern = '';

        if (!empty($this->routeParameters)) {
            foreach ($this->routeParameters as $parameter) {
                $parameterRequirment = $this->getRequirment($parameter);

                $pattern .= array_shift($pathStaticParts);

                if (!$this->isOptionalParameter($parameter)) {
                    $pattern .= "($parameterRequirment)";
                } else {
                    $pattern = rtrim($pattern, '/');
                    $pattern .= "(?:/|/($parameterRequirment)?)?";
                }
            }
            // if after last route parameter there is static parts we attach the them together
            // and append theme to $pattern
            if (count($pathStaticParts) > 0) {
                $pattern .= implode('', $pathStaticParts);
            }
        } else {
            $pattern = implode('', $pathStaticParts);
        }

        return '^' . $pattern . '$';
    }

    /**
     *
     *
     * @param string $parameter
     *
     * @return boo;
     */
    protected function isOptionalParameter($parameter)
    {
        return $parameter === $this->optionalRouteParameter;
    }

    /**
     *
     *
     * @param string $path
     *
     * @return void
     */
    protected function extractParameters($path)
    {
        $parameters = preg_match_all('`{(?P<parameters>\w+)}`', $path, $matches)
            ? $matches['parameters']
            : null;

        if ($parameters) {
            $this->setParameters($parameters);
        }
    }

    /**
     *
     *
     * @param array $parameters
     *
     * @return void
     */
    protected function setParameters(array $parameters)
    {
        if (!empty($parameters)) {
            foreach ($parameters as $param) {
                if (strpos($param, '_') === 0) {
                    $param = substr($param, 1);
                    // optional parameter MUST be at the end of $this->path if not it
                    // SHOULD NOT be optional parameter and
                    if ("{_$param}" === substr($this->path, -strlen("{_$param}"))) {
                        $this->optionalRouteParameter = $param;
                    }
                }
                $this->routeParameters[] = $param;
            }
        }
    }

    /**
     *{@inheritdoc}
     */
    public function getParameters()
    {
        return $this->routeParameters;
    }

    /**
     *
     *
     * @param stringtern
     *
     * @return $this
     */
    protected function setRegexPattern($pattern)
    {
        $this->regexPattern = $pattern;
    }

    /**
     *{@inheritdoc}
     */
    public function getRegexPattern()
    {
        return $this->regexPattern;
    }

    /**
     *
     *
     * @param array $requirments
     *
     * @return $this
     */
    protected function setRequirments(array $requirments)
    {
        foreach ($requirments as $parameter => $requirment) {
            if (!is_string($parameter) || !is_string($requirment)) {
                $msg = sprintf(
                    'All keys and values in provided argument must be string. provided key : %s and value : %s .',
                    $parameter,
                    $requirment
                );
                throw new InvalidArgumentException($msg);
            }

            $this->setRequirment($parameter, $requirment);
        }
    }

    /**
     *
     *
     * @param string $parameter
     * @param string $requirments
     *
     * @return $this
     */
    protected function setRequirment($parameter, $requirment)
    {
        if (in_array($parameter, $this->routeParameters, true)) {
            $this->parametersCondition[$parameter] = $requirment;
        }
    }

    /**
     *
     *
     * @param string $parameter
     * @param string $default
     *
     * @return string
     */
    public function getRequirment($parameter, $default = '[^/]+')
    {
        if (in_array($parameter, $this->routeParameters, true) && isset($this->parametersCondition[$parameter])) {
            return $this->parametersCondition[$parameter];
        } else {
            return $default;
        }
    }
    
    /**
     * 
     * 
     * 
     * @return <type>
     */
    public function isStatic()
    {
        return (bool) preg_match('`{\w+}`', $this->path) === false;;
    }
}