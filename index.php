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
$route->add('/about', 'About');
$route->add('/contact', 'Contact');

// when using functions
$route->add('/map', function() {
    echo 'this is a func for map';
});

echo '<pre>';
print_r($route);
echo '</pre>';

$route->submit();