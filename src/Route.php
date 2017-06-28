<?php

namespace Desa\Router;

use InvalidArgumentException;
use Desa\Router\RouteInterface;
use Desa\Router\Exceptions\BadMethodException;

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
    protected $routeParameters;

    /**
     * @var string
     */
    protected $optionalRouteParameter;

    /**
     * @var array
     */
    protected $parametersCondition;

    /**
     * @var array
     */
    private $allowedHttpMethods;

    /**
     * Route constructor.
     *
     * @param array $arguments
     */
    public function __construct(array $arguments)
    {
        $this->parametersCondition = $this->routeParameters = [];
        $this->allowedHttpMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

        $path = $arguments['path'];
        $callback = $arguments['callback'];
        $methods = isset($arguments['methods']) ? $arguments['methods'] : ['GET'];
        $requirements = isset($arguments['requirements']) ? $arguments['requirements'] : null;

        $this->setMethods($methods);
        $this->setPath($path);
        $this->setCallback($callback);
        $this->extractParameters($path);

        if ($requirements) {
            $this->setRequirements($requirements);
        }

        $this->compileRoute();
    }

    /**
     * Sets http methods for route
     *
     * @param array $methods
     *
     * @throws BadMethodException
     */
    protected function setMethods(array $methods)
    {
        foreach ($methods as $method) {
            if (!in_array(strtoupper($method), $this->allowedHttpMethods, true)) {
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
     * Sets path of the route
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
     * Gets path of the route
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets routes callback
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
     * Compile route
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
     * Builds route regex pattern
     *
     * @param array $pathStaticParts
     *
     * @return string
     */
    protected function buildRegexPattern(array $pathStaticParts)
    {
        $pattern = '';

        if (!empty($this->routeParameters)) {
            foreach ($this->routeParameters as $parameter) {
                $parameterRequirement = $this->getRequirement($parameter);

                $pattern .= array_shift($pathStaticParts);

                if ($this->isOptionalParameter($parameter)) {
                    $pattern = rtrim($pattern, '/');
                    $pattern .= "(?:/|/($parameterRequirement)?)?";
                } else {
                    $pattern .= "($parameterRequirement)";
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
     * Determine if given route parameter is optional or not
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
     * Extracts route parameters from given path
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
     * Set route parameters
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
     * Sets route regex pattern
     *
     * @param string $pattern
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
     * Sets route requirements
     *
     * @param array $requirements
     *
     * @return $this
     */
    protected function setRequirements(array $requirements)
    {
        foreach ($requirements as $parameter => $requirement) {
            if (!is_string($parameter) || !is_string($requirement)) {
                $msg = sprintf(
                    'All keys and values in provided argument must be string. provided key : %s and value : %s .',
                    $parameter,
                    $requirement
                );
                throw new InvalidArgumentException($msg);
            }

            $this->setRequirement($parameter, $requirement);
        }
    }

    /**
     * Sets route requirements
     *
     * @param string $parameter
     * @param        $requirement
     *
     * @return void
     */
    protected function setRequirement($parameter, $requirement)
    {
        if (in_array($parameter, $this->routeParameters, true)) {
            $this->parametersCondition[$parameter] = $requirement;
        }
    }

    /**
     * Gets route requirements
     *
     * @param string $parameter
     * @param string $default
     *
     * @return string
     */
    public function getRequirement($parameter, $default = '[^/]+')
    {
        if (in_array($parameter, $this->routeParameters, true) && isset($this->parametersCondition[$parameter])) {
            return $this->parametersCondition[$parameter];
        } else {
            return $default;
        }
    }
    
    /**
     * Determine if route is static or not
     * 
     * @return bool
     */
    public function isStatic()
    {
        return ((bool) preg_match('`{\w+}`', $this->path)) === false;;
    }
}