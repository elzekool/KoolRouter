<?php
namespace ElzeKool\KoolRouter\Tests;

use \PHPUnit_Framework_TestCase;
use ElzeKool\KoolRouter\Router;

/**
 * RouteTest
 * 
 * Test Route class
 * 
 * @author Elze Kool <info@kooldevelopment.nl>
 */
class RouterTest extends PHPUnit_Framework_TestCase
{
    
    public function testMatchSimple() {        
        $router = new Router();
        $called = false;                
        $router->map('/example', function() use (&$called) {
            $called = true;
        });        
        $router->run('GET', '/example');
        $this->assertTrue($called, 'Router did not match route');        
    }
    
    public function testStopMatching() {        
        $router = new Router();
        $called = 'none';                
        $router->map('/example', function() use (&$called) {
            $called = 'mapped1';
        });        
        $router->map('/example', function() use (&$called) {
            $called = 'mapped2';
        });        
        $router->run('GET', '/example');
        $this->assertEquals('mapped1', $called);       
    }
 
    public function testContinueMatchingOnFalse() {        
        $router = new Router();
        $called = 'none';                
        $router->map('/example', function() use (&$called) {
            $called = 'mapped1';
            return false;
        });        
        $router->map('/example', function() use (&$called) {
            $called = 'mapped2';
        });        
        $router->run('GET', '/example');
        $this->assertEquals('mapped2', $called);       
    }
    
    public function testExtraParameters() {        
        $router = new Router();
        $data = 'none';                
        $router->map('/example', function($method, $path, $params, $extra) use (&$data) {
            $data = $extra;
        });        
        $router->run('GET', '/example', 'extradata');
        $this->assertEquals('extradata', $data);       
    }
    
    public function testNamedReverse() {        
        $router = new Router();
        $data = 'none';                
        $router->map('/example', function() {}, 'named_test');        
        $reverse = $router->reverse('named_test', array());
        $this->assertEquals('/example', $reverse);       
    }
    
}
