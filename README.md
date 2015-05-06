Doo
===

Create simple blog with Doo.php
Developpe by `papac`

__version 0.0.1__

---
### Usage

```php
    namespace Doo;

    #Initialize your connection string
    
    require "path/to/Autoload.php"
    Autoload::register();
    
    Doo::init(sdn [, function ]);
```

`Doo::init`:

    1. sdn: is your connection string engine://username:password@hostname:port/databasename;
    
        - engine: e.g mysql
        - username: user count name in your database server.
        - password: password to connect in your database server.
        - hostname: your database server host name.
        - port: the server port default `3306`
        - databasename: select your database.
        
        e.g: mysql://papac:mypassword@sql.domaine.com/test
    2. function: take two arguments
        - $err: Object StdClass, information in error
        - $connectionConfiguration: Array, all information in your connection
        host, user, password, port

After initialzed your connection string

e.g

```php
    Doo::init("mysql://papac:mypassword@localhost:3306/test");
```
or

```php
    Doo::init("mysql://papac@localhost/test", function($err[, $connectionConfiguration])
    {
        if($err->error)
        {
            die($err->errorInfo);
        }
    });
```

#### select query
