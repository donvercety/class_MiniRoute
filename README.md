# class_MiniRoute

## Small routing php class

#### Main fiels:

- Route.php
- .thaccess

#### Example files:

- controllers/Home.php
- controllers/About.php
- controllers/Contact.php

You may put **`Route.php`** wherever you want, but **`.htaccess`**
must be in the site root folder!

#### How to use:

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
$route->add('/map', function() {
    echo 'this is a func for map';
});

$route->submit();
```
