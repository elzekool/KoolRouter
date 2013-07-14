<?php
namespace ElzeKool\KoolRouter\Tests;

use \PHPUnit_Framework_TestCase;
use ElzeKool\KoolRouter\Route;

/**
 * RouteTest
 * 
 * Test Route class
 * 
 * @author Elze Kool <info@kooldevelopment.nl>
 */
class RouteTest extends PHPUnit_Framework_TestCase
{

    public function testSimpleRoute() {        
        $called = false;        
        $route = new Route(
            '/example',
            function() use (&$called) {
                $called = true;
            }
        );       
        $route->match('GET', '/example', '');        
        $this->assertTrue($called, 'Route /example not matched correctly');
    }
    
    public function testSimpleRouteWithMethods() {        
        $called = false;
        $route = new Route(
            'GET|POST /example',
            function() use (&$called) {
                $called = true;
            }
        );       
        $route->match('PUT', '/example', '');
        $this->assertFalse($called, 'Route GET|POST /example was falsely called');
    }
    
    public function testRouteWithInteger() {        
        $matched = false;
        $id = mt_rand();
        $route = new Route(
            '/posts/[i:id]',
            function($method, $path, $params) use ($id, &$matched) {
                if (!isset($params['id'])) {
                    return;
                }
                if ($params['id'] == $id) {
                    $matched = true;
                }
            }
        );       
        $route->match('GET', '/posts/' . $id, '');
        $this->assertTrue($matched, 'Route /posts/[i:id] not matched correctly');
    }
    
    public function testRouteWithOptional() {        
        $called = false;
        $route = new Route(
            '/posts[/index]?',
            function() use (&$called) {
                $called = true;
            }
        );       
        $route->match('GET', '/posts/','');
        $this->assertTrue($called, 'Route /posts[/index]? not matched correctly');
    }
    
    public function testComplexRoute() {        
        $matched = false;
        $title = sha1(mt_rand());
        $id = mt_rand();
        $route = new Route(
            '/posts/[*:title]-[i:id]',
            function($method, $path, $params) use ($id, $title, &$matched) {
                if ((!isset($params['title'])) OR (!isset($params['id']))) {
                    return;
                }
                if (($params['title'] == $title) AND ($params['id'] == $id)) {
                    $matched = true;
                }
            }
        );       
        $route->match('GET', '/posts/' . $title . '-' . $id, '');
        $this->assertTrue($matched, 'Route /posts/[*:title]-[i:id] not matched correctly');
    }
    
    public function testReverseRouteSimple() {
        $route = new Route('/example', function() {}, 'simple');
        $reverse = $route->reverse();
        $this->assertEquals('/example', $reverse);
    }
 
    public function testReverseRouteWithInteger() {
        $route = new Route('/posts/[i:id]', function() {}, 'simple');
        $reverse = $route->reverse(array('id' => 10));
        $this->assertEquals('/posts/10', $reverse);
    }
    
    public function testReverseRouteComplex() {
        $route = new Route('/posts/[a:title]-[i:id]', function() {}, 'simple');
        $reverse = $route->reverse(array('title' => 'test', 'id' => 10));
        $this->assertEquals('/posts/test-10', $reverse);
    }
    
    
}
