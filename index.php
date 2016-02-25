<?php

/* Including Route.php Class */
require ('Route.php');
require ('controllers/Home.php');
require ('controllers/Contact.php');
require ('controllers/About.php');

/* Instantiating new $route Object */
$route = new Route();

// when using Classes
$route->add('/', 'Home');
$route->add('/h', 'Home');

$route->add('/about', 'About');
$route->add('/contact', 'Contact');

// when using functions
$route->add('/map', function() use ($route) {
    echo 'this is a func for map';
	var_dump($route->getParams());
});

echo '<pre>';
print_r($route);
echo '</pre>';

$route->submit();