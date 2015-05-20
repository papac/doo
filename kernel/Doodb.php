<?php

/**
* PHP, une classe PHP simple. Dans le but simplifié l'utilisation de PDO
* @author Dakia Franck <dakiafranckinfo@gmail.com>
* @package Doo;
* @version 0.1.0
*/

namespace Doo;

class Doodb {

    /**
    * creation d'un connection PDO
    *
    * @param array
    * @param callback function
    * @return PDO || NULL
    * e.g mysql://username:password@hostname:port/dbname
    */

    public static function connection($cb = null){

        $stream = @file("../appconfig/app.conf");

        if(!$stream)
        {

            if ($cb !== null)
            {

                call_user_func($cb, new Exception("Vérifiez le chemin de fichier app.conf situer dans appconfig"));

            }
            else
            {

                throw new Exception("Vérifiez le chemin de fichier app.conf situer dans appconfig");

            }

        }

        $dsn = preg_replace("#[A-Z]+=|\n#", "", base64_decode($stream[0]));

        $tmp = explode("/", $dsn);

        $config = explode("@", $tmp[2]);
        $userConfig = explode(":", $config[0]);
        $hostConfig = explode(":", $config[1]);

        $host = $hostConfig[0];
        $port = isset($hostConfig[1]) ? $hostConfig[1] : '';

        $user = $userConfig[0];
        $password = isset($userConfig[1]) ? $userConfig[1] : '';

        $dbname = $tmp[3];
        $engine = $tmp[0];


        # create de l'apercu externe.
        $connectionData = [
            "engine" => $engine,
            "host" => $host,
            "port" => $port,
            "user" => $user,
            "password" => $password,
            "dbname" => $dbname
        ];

        try{

            # Instantiation de la connection via le driver PDO
            $bdd = new \PDO("{$engine}:host={$host};dbname={$dbname}", "{$user}", "{$password}");

        }catch(\Exception $e){
            # gestion d'exception sur la chaine de connection PDO

            if($cb !== null)
            {
                # is elle n'est pas null, execution de la fonction de rappel
                call_user_func_array($cb, [$e, $connectionData]);
            }

            return null;

        }

        if($cb !== null)
        {
            # is elle n'est pas null, execution de la fonction de rappel
            call_user_func_array($cb, [null, $connectionData]);
        }

        # Retour de l'objet PDO
        return $bdd;

    }

}
