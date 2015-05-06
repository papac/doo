<?php


  namespace Doo;

  class Doodb {
    
    /**
    * creation d'un connection PDO
    * @param array 
    * @param callback function
    * @return PDO || NULL
    * e.g mysql://username:password@hostname:port/dbname
    */

    public static function connection($sdn, $cb = null){

        $tmp = explode("/", $sdn);

        $config = explode("@", $tmp[2]);
        $userConfig = explode(":", $config[0]);
        $hostConfig = explode(":", $config[1]);
        $host = $hostConfig[0];
        $port = isset($hostConfig[1]) ? $hostConfig[1] : '';
        $user = $userConfig[0];
        $password = isset($userConfig[1]) ? $userConfig[1] : '';
        
        $dbname = $tmp[3];
        $engine = $tmp[0];

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
        $bdd = new \PDO("{$engine}:host={$host};dbname={$dbname}", "{$user}", "${password}");

      }catch(\Exception $e){
        # gestion d'exception sur la chaine de connection PDO
        
        if($cb !== null)
        {
          $cb($e, $connectionData);
        }

        return null;

      }

      if($cb !== null)
      {
        $cb(null, $connectionData);
      }

      # Retour de l'objet PDO
      return $bdd;

    }

  }
