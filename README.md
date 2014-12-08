# php.class.MiniRoute v1.0

Version 1.1

The value after the " / " makes a controller callsback. Which can be a separate class or a function.  
For example **http://mysite.com/contacts** will call the controller called **Contacts**. This controller  
can be a php Class or a simple function.

**mod_rewrite must be enabled for this to work!**

#### Main fiels:

- Route.php
- .hthaccess

#### Example files:

- index.php
- controllers/Home.php
- controllers/About.php
- controllers/Contact.php

#### How to use:

You may put **`Route.php`** wherever you want, but **`.htaccess`**
must be in the site root folder!

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
$route->add('/map', function() {
    echo 'this is a func for map';
});

$route->submit();
```

