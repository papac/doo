### Doo
Doo est un systeme simple écris en POO.
Concevez votre site web simple avec `Doo`, avec un codage élégant.

Developpe by Franck Dakia

lien
    - [facebook](https://wwww.facebook.com/programmeurFou)
    - [twitter](https://www.twitter.com/dakiaFranck)

## Déscription de `Doo`
* Selectionner, suprimez, mise à jour et insertion de donnée dans une table quelconque.
* Envoyer des mails en mixed/multipart, (html ou text-simple).
    - Pièce-joints
    - Object-oriented.
    - Utilisant la fonction mail().
* Manipuler les dates plus flexiblement.
* Cripter vos données.

### Comment ça marche (Basic).
#### Connection à la base de donnée.
```php

    use \Doo\Doo;
    use \Doo\Autoload;

    require "path/to/Autoload.php"

    Autoload::register();

    Doo::init(function($status)
    {
        if($status !== null)
        {
            die($status->getMessage());
        }
    });
```
#### Selectionner dans données.
```php
    
    # Serveur de base de donnée déjà connecté.
    
    $collection = new StdClass;
    
    $cb = function($err, $res) use (&$collection){

        if($err->error)
        {
            die($err->errorInfo);
        }

        $collection->res = $res;
        
    };

    Doo::select("nomDeLaTable", ["colonne1", "colonne2", "..."], $cb);

    var_dump($collection->res);
```

#### Inserer des données
```php
    
    # Serveur de base de donnée déjà connecté.

    $collectionDeDonneeAInserer = [
        "id" => (int) $_POST["id"],
        "name" => addslashes($_POST["name"])
    ];

    Doo::insert("table", $collectionDeDonneeAInserer, function($err)
    {
        if($err->error)
        {
            die($err->errorInfo);
        }
    });
```

#### Supprimer ou mise à jour des données
```php
    
    # Serveur de base de donnée déjà connecté.

    Doo::[delete|update]("table", ["id" => 1], function($err)
    {
        
        if($err->error)
        {
            die($err->errorInfo);
        }

    });
```

#### Envoie de mail
```php
    
    # manespace charger
    use \Doo\Doo;
    use \Doo\Autoload;

    Autoload::register();

    # Factultative.
    Doo::mail()
    ->setMailServer("smpt.gmail.com")
    ->setPort(587)
    ->addAttachementFile("chemin/vers/le/fichier")
    ->addAttachementFile("chemin/vers/le/fichier")
    ->addHeader("Cc", "john@autre.domaine.mail.com");
    ->addHeader("Bcc", "john@autre.domaine.mail.com");

    # Par defaut.
    Doo::mail()
    ->to("dakiafranckinfo@gmail.com")
    ->from("joe@mail.com")
    ->subject("Doo-Mail")
    ->send(function($status)
    {
        if($status)
        {
            echo "Mail envoyé.";
        }
        else
        {
            echo "Mail non envoyé";
        }
    });

    # Doo::mail, Utilise un le design pattern singleton.
```