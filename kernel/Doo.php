<?php

  namespace Doo;

  class Doo {

    /**
    * Super constante, permetant de simplifier le information sur les erreurs
    */
    const NOTERROR =  "<span style=\"color:red\">NOT ERROR</span>";
    const ERROR =  "<span style=\"color:red\">ERROR</span>";
    const SUCESS =  "<span style=\"color:red\">SUCESS</span>";
    const FAILURE =  "<span style=\"color:red\">FAILURE</span>";

    # configuration du jeu de caractere
    private static $charset = null;

    # Le mode de recuperation de des
    private static $modeDeRecuperationDeDonnee = \PDO::FETCH_OBJ;

    /**
    * initialisation de la chaine de connection
    * e.g mysql://username:password@hostname:port/dbname
    */
    private static $bdd = null;

    public static function init($sdn, $cb = null)
    {

      self::$bdd = Doodb::connection($sdn, $cb);
      if(self::$bdd !== null)
      {
        self::$bdd->exec("SET NAMES UTF8");
      }

    }

    /**
    * setCharset, permet de reinitialiser le jeux de caracter
    * @param strting: encodage
    * @return mixed
    */

    public static function setCharset($charset, $cb = null)
    {

      if(!is_string($charset)){
        return $cb(new Exception('encodage non valide'));
      }

      self::$charset = $charset;

      $cb(null);

    }

    public static function setFetchMode($pdoFetchMode)
    {

      self::$modeDeRecuperationDeDonnee = (int) $pdoFetchMode;

    }

    /**
    * select: fonction permettant d'executer tout forme de requette de type SELECT
    * @param table string: la table sur laquelle faire la selection
    * @param fields array: liste de colonne à visualiser
    * @param cb function: fonction de recuperation des erreurs et des donnees
    * @param order booleen: ORDER BY
    * @param limit string: LIMIT
    */

    public static function select($table, $fields, $cb, $where = null, $order = false, $limit = null)
    {


      /**
      * Utilisation global de la variable $bdd
      */
      # Une chaine contenant un liste de colonne des elements en visualiser
      $fields = implode(", ", $fields);

      # Statement par defaut
      $query = "SELECT " . $fields . " FROM " . $table;


      # Construction d'un SQL statement personnalisable
      if($where !== NULL && is_array($where)){

        if(count($where) == 1){

          $query .= " WHERE " . implode(",", $where);

        }else{

          if(is_string($order)){

            $limit = $order;
            $order= $where;

          }

        }

      }elseif(is_string($where)){

        $limit = $where;

      }

      if(is_array($order) && count($order) == 2){

        if(end($order) === true){

          $query .= " ORDER BY " . $order[0] . " DESC";

        }else{
          $query .= " ORDER BY " . $order[0] . " ASC";
        }

      }elseif(is_string($order)){
        $limit = $order;
      }

      if($limit !== null){
        $query .= " LIMIT " . $limit;
      }

      $req = self::$bdd->query($query);

      if($self::$charset !== null)
      {
        $self::$bdd->exec("SET NAMES " . self::$charset);
      }

      $err = self::getError(self::$bdd->errorInfo(), $query);

      if(is_bool($req)){

        $cb($err, []);

      }else{

        $cb($err, $req->fetchAll(self::$modeDeRecuperationDeDonnee));

      }

    }

    /**
    * update: fonction permettant d'executer tout forme de requette de type UPDATE
    * @param table string: la table sur laquelle faire la selection
    * @param fields array: liste de colonne à mettre a jour
    * @param cb function: fonction de recuperation des erreurs et des donnees
    * @param where string: where condition
    */

    public static function update($table, $fields, $where, $cb){

      $query = "UPDATE " . $table . " SET " . implode(", ", $fields) . " WHERE " . $where;

      self::$bdd->exec($query);

      $cb(self::getError(self::$bdd->errorInfo(), $query));

    }

    /**
    * insert: fonction permettant d'executer tout forme de requette de type INSERT
    * @param table string: la table sur laquelle faire la selection
    * @param fields array: liste de colonne à inserer
    * @param cb function: fonction de recuperation des erreurs et des donnees
    */

    public static function insert($table, $fields, $cb){

      $query = "INSERT INTO {$table} SET ";

      $i = 0;

      foreach ($fields as $key => $value)
      {

        $query .= $i > 0 ? ", " : "";
        $query .=  "{$key} = :{$key}";
        $i++;

      }

      $req = self::$bdd->prepare($query);

      foreach($fields as $key => $value)
      {
        $req->bindValue("{$key}", $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
      }

      $req->execute();

      $cb(self::getError(self::$bdd->errorInfo(), $query));

    }


    /**
    * delete: fonction permettant d'executer tout forme de requette de type DELETE
    * @param table string: la table sur laquelle faire la selection
    * @param where array: id des colonnes à supprimer
    * @param cb function: fonction de recuperation des erreurs et des donnees
    */

    public static function delete($table, $where, $cb){

      $query = "DELETE FROM " . $table . " WHERE id = :id";

      $req = self::$bdd->prepare($query);
      $req->bindValue(":id", $where["id"], \PDO::PARAM_INT);

      $req->execute();

      $cb(self::getError(self::$bdd->errorInfo(), $query));

    }

    public static function uploadFile($file, $extension = null, $cb = null)
    {

      if(is_string($extension))
      {
        $extensionValide = explode(", ", $extension);
      }
      else
      {
        $cb = $extension;
      }

      if(is_uploaded_file($file["tmp_name"]))
      {

        if($file["error"] === 0)
        {

          if($file["size"] <= 2000000)
          {

            $pathInfo = pathinfo($file["name"]);

            if(in_array($pathInfo["extension"], $extensionValide))
            {

              $filename = md5(uniqid(rand(null, true)));
              $ext = $pathInfo['extension'];

              move_uploaded_file($file["tmp_name"], '../public/image/' . $filename . '.' . $ext);

              $status = self::SUCESS;

            }
            else
            {

              $status = self::FAILURE . ' : Fichier non valide, verifiez le type de fichier.';

            }

          }
          else
          {

            $status = self::FAILURE . ' : Fichier trop grop, 2Mo au maximum.';

          }

        }
        else
        {

          $status = self::FAILURE . ' : Le fichier possède des erreurs.';

        }

      }
      else
      {

        $status = self::FAILURE . ' : Le fichier n\'a pas pus être uploader.';

      }

      $cb($status, isset($filename) ? $filename: null, isset($ext) ? $ext : null);

    }

    /**
    * getError est une fonction permetant de formater des erreurs, et nous les envoyes
    * @param array, liste des erreurs generer pas PDOException
    * @param string, la requete sur laquelle il y a eu l'erreur
    * @return object, un objet contenant les informations formates de l'erreur
    */
    private static function getError($err, $query){

      return (object) [
        "error" => $err[2] !== null ? true: false,
        "query" => $query,
        "errorInfo" => $err[2] !== null ? self::ERROR .": ". str_replace("' ", " ", preg_replace("#'[a-zA-Z_-]+\.#", "", $err[2])) : self::NOTERROR
      ];

    }

  # Fichier, contenant un code simple en php, nous permettant d'executer de requete SQL.
  }
