<?php

/**
* PHP, une classe PHP simple. Dans le but simplifié l'utilisation de PDO
* @author Dakia Franck <dakiafranckinfo@gmail.com>
* @package Doo;
* @version 0.1.0
*/
namespace Doo;

abstract class Doodb {

    /**
     * @param null $dsn
     * @param null $cb
     * @return null|\PDO
     * @throws \Exception
     */
    protected static function connection($r = null, $cb = null){

        $dir = str_replace("\\", "/", __DIR__);
        
        if($r !== null) {
            
            if(!is_string($r)) {
            
                $cb = $r;
                $r = self::readConfigurationFile($dir);

            }

        } else {

            $r = self::readConfigurationFile($dir);
            
        }

        self::errorListener($r, $cb);

        $d = self::makeDsn($r);

        # Mise en forme de configuration de dns

        try {

            # Instantiation de la connection via le driver PDO
            $bdd = new \PDO($d->dsn, $d->user, $d->pass);

        } catch(\Exception $e) {

            # Gestion d'exception sur la chaine de connection PDO

            if($cb !== null) {
                # Is elle n'est pas null, execution de la fonction de rappel
                call_user_func($cb, $e);

            }

            return null;

        }

        if($cb !== null) {

            # Is elle n'est pas null, execution de la fonction de rappel
            call_user_func($cb, null);

        }

        # Retour de l'objet PDO
        return $bdd;

    }

    /**
    * readConfigurationFile, permet de lire les informations de configuration
    * @param string $dir, le repertoir parent
    * @return strting $r, la chaine dsn
    */
    private static function readConfigurationFile($dir) {

       if(is_dir("{$dir}/.config")) {

            $r = base64_decode(file_get_contents("{$dir}/../.config/.dsn"));
            
        } else {

            $r = null;

        }


        return $r;
    }

    /**
    * errorListener, permet de lancer des erreurs en cas de chaine dsn non valide ou inexistante
    * @param string $r, la chaine de connection
    * @param callable $cb, fonction de rappelle, facultative
    */
    private static function errorListener($r, $cb) {

        if($r === null) {

            if($cb !== null) {

                call_user_func($cb, new DooException("Dsn-not-created"));
                die();

            } else {

                throw new DooException("Dsn-not-created");

            }

        }

    }

    /**
    * makeDsn, permet d'ordonner le dsn
    * @param $r
    * @return object
    */
    private static function makeDsn($r) {

        $r = preg_replace("#[A-Z]+=|\n#", "", $r);
        $r = parse_url($r);
        $d = new \StdClass;

        foreach(["scheme", "user", "host", "path", "pass", "port"] as $key => $value) {

            if(isset($r[$value])) {

                $d->{$value} = trim($r[$value], "/");
            
            } else {

                $d->{$value} = '';

            }

        }

        return (object) [

            "dsn" => $d->scheme . ":host=" . $d->host . "" . ($d->port !== '' ? ":". $d->port: "") . ";dbname=". $d->path,
            "user" => $d->user,
            "pass" => $d->pass

        ];

    }

}
