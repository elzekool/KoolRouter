<?php
namespace ElzeKool\KoolRouter;

/**
 * Routing class that can be used for matching URL's against (named)
 * routes. Also allowes reverser routing, where a named routed is converted
 * to the correct URL.
 * 
 * @author Elze Kool <info@kooldevelopment.nl>
 */
class Router
{
    /**
     * Routes
     * @var IRoute[]
     */
    private $Routes = array();
    
    /**
     * Map a new route
     * 
     * Example routes:
     * - /pages/view/[i:id]
     * - GET|POST /pages
     * - GET|PUT|DELETE /pages/[i:id]
     * - GET /output.[xml|json:format]?
     * 
     * Callback should have the following signature:
     *  function($method, $path, $parameters, $extra) : boolean|null
     *  Routing is continued on returning false, else routing is stopped,
     *  and indicated as succesfully matched
     * 
     * @param string   $route    Route to match
     * @param callable $callback Callback to run on succesfull match
     * @param string   $name     Name that can be used for reverse routing
     * 
     * @return void
     */
    public function map($route, $callback, $name = null)
    {
        if (!is_callable($callback)) {
            // note; PHP 5.4+ has a new signature to denote a callable.
            // In future versions this should be used, use this check for now.
            throw new \InvalidArgumentException('Callback is not a valid callable');
        }        
        
        if (($name !== null) AND array_key_exists($name, $this->Routes)) {
            throw new \InvalidArgumentException('Named route ' . $name . ' is already defined');
        }
        
        if ($name === null) {
            $this->Routes[] = new Route($route, $callback);
        } else {
            $this->Routes[$name] = new Route($route, $callback);
        }
        
    }
    
    /**
     * Start routing
     * 
     * Use this function to start the actual routing after mapping all 
     * routes. It can determine the used request method and path from the
     * $_SERVER variabele ($_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']).
     * 
     * @param string $method Request method. null to retrieve it from $_SERVER
     * @param string $path   Request path. null to retrieve it from $_SERVER
     * @param mixed  $extra  Extra data to pass on to callback
     * 
     * @return boolean Matched route
     */
    public function run($method = null, $path = null, $extra = null)
    {
        if ($method === null) {
            $method = $_SERVER['REQUEST_METHOD'];
        }
        
        if ($path === null) {
            $path = !empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
        }
        
        foreach($this->Routes as $route) {
            if ($route->match($method, $path, $extra)) {
                return true;
            }            
        }
        
        return false;
    }
     
    /**
     * Create Path (URL) from named route 
     * 
     * @param string $name       Name of route (set with map)
     * @param mixed  $parameters Parameters for route
     * 
     * @return string
     */
    public function reverse($name, $parameters) {
        if (!isset($this->Routes[$name])) {
            throw new \InvalidArgumentException(
                'Route ' . $name . ' not found, did you forgot to map it?'
            );
        }
        return $this->Routes[$name]->reverse($parameters);
    }
    
}