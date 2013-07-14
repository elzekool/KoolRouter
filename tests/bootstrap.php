<?php
/**
 * Routing class that can be used for matching URL's against (named)
 * routes. Also allowes reverser routing, where a named routed is converted
 * to the correct URL.
 * 
 * @author Elze Kool <info@kooldevelopment.nl>
 */

// Load autoloader, and add our Test class namespace
$autoloader = require(__DIR__ . '/../vendor/autoload.php');
$autoloader->add('ElzeKool\KoolRouter\Tests', __DIR__);