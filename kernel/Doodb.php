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
     * @param null $dsn
     * @param null $cb
     * @return null|\PDO
     * @throws \Exception
     */
    public static function connection($dsn = null, $cb = null){

        if($dsn !== null)
        {

            if(!is_string($dsn))
            {

                $cb = $dsn;

            }
        }

        if($dsn === null)
        {
            $stream = @file("../appconfig/app.conf");

            if(!$stream)
            {

                if ($cb !== null)
                {

                    call_user_func($cb, new \Exception("Vérifiez le chemin de fichier app.conf situer dans appconfig"));

                }
                else
                {

                    throw new \Exception("Vérifiez le chemin de fichier app.conf situer dans appconfig");

                }

            }

            $dsn = preg_replace("#[A-Z]+=|\n#", "", base64_decode($stream[0]));
        }

        $tmp = explode("/", $dsn);

        $config = @explode("@", $tmp[2]);
        $userConfig = @explode(":", $config[0]);
        $hostConfig = @explode(":", $config[1]);

        $host = @$hostConfig[0];
        $port = isset($hostConfig[1]) ? @$hostConfig[1] : '';

        $user = @$userConfig[0];
        $password = isset($userConfig[1]) ? @$userConfig[1] : '';

        $dbname = @$tmp[3];
        $engine = @$tmp[0];

        try{

            # Instantiation de la connection via le driver PDO
            $bdd = new \PDO("${engine}:host=${host};dbname=${dbname}", "${user}", "${password}");

        }catch(\Exception $e){
            # gestion d'exception sur la chaine de connection PDO

            if($cb !== null)
            {
                # is elle n'est pas null, execution de la fonction de rappel
                call_user_func($cb, $e);
            }

            return null;

        }

        if($cb !== null)
        {
            # is elle n'est pas null, execution de la fonction de rappel
            call_user_func($cb, null);
        }

        # Retour de l'objet PDO
        return $bdd;

    }

}
