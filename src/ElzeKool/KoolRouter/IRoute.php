<?php
namespace ElzeKool\KoolRouter;

/**
 * Route interface used by Router.
 * 
 * @see \ElzeKool\KoolRouter\Router
 * @author Elze Kool <info@kooldevelopment.nl>
 */
interface IRoute
{

    /**
     * Constructor.
     * 
     * @param string   $route    Route to match
     * @param callable $callback Callback to call on successfull match
     */
    public function __construct($route, $callback);

    /**
     * Match Path and call callback in case of a succesfull match
     * 
     * @param string $method Request method (GET|POST|PUT|DELETE|HEAD|OPTIONS)
     * @param string $path   Path (URL)
     * @param string $extra  Extra data that should be passed on to callback
     * 
     * @return boolean Matched route
     */
    public function match($method, $path, $extra);
    
    
    /**
     * Do a reverse route, create an URL (Path) from a set of parameters.
     * 
     * @param mixed $parameters Parameters (most of the times an array)
     * 
     * @return string URL (Path)
     */
    public function reverse($parameters = array());
    
    
}