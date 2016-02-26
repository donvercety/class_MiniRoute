# php.class.MiniRoute v2.1

Version 2.1

The value after the " / " makes a controller callback. Which can be a separate class or a function. For
example **http://mysite.com/contacts** will call the controller called **Contacts**. This controller can be a php Class or a simple function. This functionality is from [v1.1](https://github.com/donvercety/php.class.MiniRoute/archive/v1.1.zip).   
In [v2.0](https://github.com/donvercety/php.class.MiniRoute/releases/tag/v2.0) the ability to target a specific method in the class is implemented and also the ability to receive url parameters in the callbacks.  
In v2.1 the `$params` array is removed. Parameters are now reachable by the `Route` class instance `$route->getParams()`. By popular demand I added a way to reach the query string parameters `$route->getData()`.   
In v.2.2 The routing algorithm is improved.  
The default targeted method is **index()**, so if you have:

```
http://mysite.com/home
```
The class **Home** will be called and the method **index()** will be executed.

```
http://mysite.com/home/map
```

The class **Home** will be called and the method **map()** will be executed. If the method map is not present in the chosen class, this value "map" will be added to the params array.  

The params array, is an array that will contain all the parameters after the controller/method:

```
http://mysite.com/home/map/1/NewYork
```
This will execute **Home** controller **map()** method and it will pass two parameters.

#### Main fiels:

- `Route.php`
- `.htaccess` - *optional*

#### Example files:

- `index.php`
- `controllers/Home.php`
- `controllers/About.php`
- `controllers/Contact.php`

#### How to use:

You may put **`Route.php`** wherever you want, but **`.htaccess`**  
must be in the site root folder! The `.htaccess` file is used to remove   
the need of the `/index.php/` file call in the URI.

##### File: .htaccess
```
RewriteEngine On
RewriteBase /projects/class_MiniRoute/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

RewriteRule ^(.+)$ index.php?uri=$1 [QSA,L]
```

Replace `/projects/class_MiniRoute/` with your site path.

##### File: index.php
```php
<?php

/* Including Route.php Class */
require ('Route.php');

/* Including controllers */
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
$route->add('/map', function() use ($route) {
    echo 'this is a func for map';
	var_dump($route->getParams());
});

$route->submit();
```

To be able to receive parameters in a Class method or function callback,  
you need to pass the instance of the Route class.

```php
$route->add('/about', function() use ($route) {
	// pretty parameters
    $route->getParams();

    // query string parameters
    $route->getData();
});
```

```php
class About {

	function index(Route $route) {
		// pretty parameters
        $route->getParams();

        // query string parameters
        $route->getData();
	}
}
```

