<?php
namespace ElzeKool\KoolRouter;

/**
 * Default Route implementation used by Router.
 *
 * @see \ElzeKool\KoolRouter\Router
 * @author Elze Kool <info@kooldevelopment.nl>
 */
class Route implements IRoute
{

    /**
     * Route to match
     * @var string
     */
    protected $Route;

    /**
     * Callback to call on successfull match
     *
     * Should have the following signature
     * function($method, $path, $parameters, $extra) : boolean|null
     *
     * @var callable
     */
    protected $Callback;

    /**
     * Allowed methods. Filled on compiling route
     * @var null|string[]
     */
    private $Methods;

    /**
     * Path
     * @var string
     */
    private $Path;

    /**
     * Compiled regex for matching route
     * @var string
     */
    private $Regex;

    /**
     * List of field names in route
     * @var string[]
     */
    private $FieldNames = array();


    /**
     * Constructor.
     *
     * @param string   $route    Route to match
     * @param callable $callback Callback to call on successfull match
     */
    public function __construct($route, $callback)
    {
        $this->Route = $route;
        $this->Callback = $callback;
    }

    /**
     * Compile route into method list and parts
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    private function compile()
    {
        // Check if route matches our expectations
        $parts = array();
        if (preg_match(
            '#^(?:(?P<methods>(?:(?:GET|POST|PUT|DELETE|HEAD|OPTIONS)\|?)*)\s)?(?P<path>[^\s]+)$#',
            $this->Route,
            $parts
        )) {
            $this->Methods = isset($parts['methods']) ? explode('|', trim($parts['methods'])) : null;
            $path = $parts['path'];
        } else {
            throw new \InvalidArgumentException(
                'Invalid route format, route should look like GET|POST /example/[i:id]'
            );
        }

        // No do further processing of path...

        if ($path[0] != '/') {
            throw new \InvalidArgumentException(
                'Invalid route format, path should start with /'
            );
        }

        // Remove trailing slash
        if (substr($path, -1) == '/') {
            $path = substr($path, 0, -1);
        }

        $this->Path = $path;

        $match_types = array(
            'i'  => '[0-9]++',
            'a'  => '[0-9A-Za-z]++',
            'h'  => '[0-9A-Fa-f]++',
            '*'  => '.+?',
            '**' => '.++',
            ''   => '[^/]++'
        );

        // Create regex that can be used for matching
        $field_names = array();
        $this->Regex = preg_replace_callback(
            '~(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)~',
            function ($part) use ($match_types, &$field_names) {
                list($match, $sep, $type, $name, $optional) = $part;

                // Check if type is in list of predefined types
                // else tread it als an regular expression
				if (isset($match_types[$type])) {
					$type= $match_types[$type];
				}

                // Segments can be split on / and .
                // Escape the latter
				if ($sep == '.') {
					$sep = '\.';
				}

                // Check if we should make it a named group that can be
                // used for retrieving parameter
                if ($name != '') {
                    $field_names[] = $name;
                    $match_group = '(?P<' . preg_quote($name) . '>' . $type . ')';
                } else {
                    $match_group = '(?:' . $type . ')';
                }

                // Create full regex
                $match = '(?:' . $sep . $match_group . ')' . $optional;

                // Return replacement
                return $match;

            },
            '^' . $path . '$'
        );

        // Save field names
        $this->FieldNames = $field_names;

        // Add PCRE delimiters
        $this->Regex = '~' . str_replace('~', '\~', $this->Regex) . '~';

    }


    /**
     * Match Path and call callback in case of a succesfull match
     *
     * @param string $method Request method (GET|POST|PUT|DELETE|HEAD|OPTIONS)
     * @param string $path   Path (URL)
     * @param string $extra  Extra data that should be passed on to callback
     *
     * @return boolean Matched route
     */
    public function match($method, $path, $extra)
    {
        // Check if route is compiled
        if ($this->Regex === null) {
            $this->compile();
        }

        // Check request method
        if (($this->Methods !== null) AND !in_array($method, $this->Methods)) {
            return false;
        }

        // Always add starting slash
        if ($path[0] != '/') {
            $path = '/' . $path;
        }

        // Remove trailing slash
        if (substr($path, -1) == '/') {
            $path = substr($path, 0, -1);
        }

        // Call callback
        $params = array();
        if (preg_match($this->Regex, $path, $params)) {

            // Remove matches not in field list
            $params = array_intersect_key($params, array_flip($this->FieldNames));

            if (false === call_user_func($this->Callback, $method, $path, $params, $extra)) {
                return false;
            } else {
                return true;
            }

        }

        return false;

    }


    /**
     * Do a reverse route, create an URL (Path) from a set of parameters.
     * Return null in case reverse routing failes
     *
     * @param mixed $parameters Parameters (most of the times an array)
     *
     * @return string URL (Path)
     */
    public function reverse($parameters = array())
    {
        $url = preg_replace_callback(
            '~(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)~',
            function ($part) use ($parameters) {
                list($match, $sep, , $name, $optional) = $part;

                if (empty($name)) {
                    throw new \InvalidArgumentException(
                        'Element ' . $match . ' cannot be reverse routed becouse it has no name.'
                    );
                }

                // Return parameter
                if (array_key_exists($name, $parameters)) {
                    return $sep . $parameters;

                // Else check if parameter was optional
                } elseif ($optional != '?') {
                    throw new \InvalidArgumentException(
                        'Parameter ' . $name . ' not provided.'
                    );
                }

                return $sep;

            },
            $this->Path
        );

        return $url;
    }


}