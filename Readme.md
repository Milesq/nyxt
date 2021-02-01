# Nyxt - modern & simple PHP framework

## Installation

You use this framework by [composer](https://getcomposer.org/)

`composer require milesq/nyxt`

## Out of the box

What is included in this package?

-   Routing based on file system (custom 404, public directory)
-   Twig template engine
-   Form validation based on rakit/validation
-   Simple a'la ORM to help you manage your database (based on clancats/hydrahon)

## Using

Check our `examples/` directory

### Before start

Nyxt have a small boilerplate. You must redirect all requests (except request which starts from `/public`) to index.php

Example configuration for Apache

```apache
RewriteEngine On

RewriteRule ^(app|dict|ns|tmp)\/|\.ini$ - [R=404]

RewriteCond %{REQUEST_FILENAME} !-l
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(?!public/)(.+)$ index.php [L,QSA]
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]
```

Now you can simply run framework from `index.php`

```php
<?php
require_once './vendor/autoload.php';

$framework = new \Nyxt\Base;
$framework->run();
```

Then create a folder named `controllers`.
This directory is the place for your routes.
Inside controller files you must declare class
called `Handler` which extends from `\Nyxt\Controller`.
This class should have public handle function which will
be invoked when someone sends a request to your endpoint.

Example of handler

```php
<?php
class Handler extends \Nyxt\Controller {
    public function handle() {
        // There you can handle request
        echo 'URL is: /';
    }
}
```

### Routing

Routing is based on file system and inspired by [Nuxt](https://nuxtjs.org/)

There is a few rules you need to know to create routes

-   `index.php` will take control over `/` path
-   `something.php` can be achieved by `/something`
-   `create.php` inside `user` can be achieved by `/user/create`
-   you can add path parameters (slug) by prepend name of slug with `_`.

    For example `controllers/user/_id.php` can be achieved by `/user/what-ever`
    You have access to slug parameters by handler object like that: `$this->id`

For the following file structure, the following paths will be available:

```
|   .htaccess
|   index.php
|
\---controllers
    |   index.php               /
    |
    \---user
        \---_id
                create.php      /user/what-ever/create
                _action.php     /user/what-ever/name-of-action
```

Check `examples/routing` for more tips

### Templates

Inside `templates/`directory you can place
twig templates, nextly you can render
them inside controller by `$this->render($name, $parametersAsAssocTable)`

**Important** Remember to set environment
variable `NYXT_MODE` to production on deploy server.
In development mode, cache is not used.

You can set template params through for a few ways
E.g.

```php
class Handler extends \Nyxt\Controller {
    public function handle() {
        // You can declare template arguments like:
        $this->by_property = "hello";
        $this->reset();
        $this
            ->setByMethod("hello")
            ->setChainMethod("world")
            ->unset('chainMethod');

        $this->render('index', ['by_arg' => 1]);
    }
}
```

### Validation

Every handler can declare `validate` method.
The method will be invoked with `$v`
parameter which is an instance of `\Rakit\Validation\Validator`.
Check out [https://github.com/rakit/validation](https://github.com/rakit/validation)

The `validate` method must tell Next if the validation passed
via return boolean or string.

### Error 404 - not found

You can apply your own 404 page by add `[error].html`
template or `404.html` in public directory

### Using DB

ORM is based on clancats/hydrahon,
so check out [docs](https://clancats.io/hydrahon/master/)
and `examples/orm`

To create a model, you need to create a file
named which is a singular form of the db table.

Example model:

```php
<?php
class Bike extends \Nyxt\Model {
    var array $__columns = ['position', 'docked']; # these columns will be used in select and create methods

    public function docked() {
        return $this->findByDocked(true);
    }
}
```

How can we use this model?

When `Handler` class is decorated with `#[nyxt('orm')]`
every model will be injected to handler

```php
#[nyxt('orm')]
class Handler extends \Nyxt\Controller {
    function handle() {
        $this->bikes->docked()->where('id', '>', 3)->get();
    }
}
```

To learn how exactly can u build queries take a look for hydrahon docs

#### **Important**

Now you may be asking "how does Nyxt connect to the database?"

Answer: If your app is using db, you must change index.php a little and provide db connector as the first argument of constructor of \Nyxt\Base

E.g.

```php
$framework = new \Nyxt\Base(function() {
    return new PDO("mysql:host=localhost;dbname=test", "username", "pass");
});
```
