<?php

/**
* PHP, une classe PHP simple. Dans le but simplifié l'utilisation de PDO
* @author Dakia Franck <dakiafranckinfo@gmail.com>
* @package Doo;
* @version 0.1.0
*/
namespace Doo;

/**
 * Class Doo
 *
 * @package Doo
 */
class Doo {

    /**
    * Super constante, permetant de simplifier le information sur les erreurs
    */
    const NOTERROR =  "<span style=\"color:red\">NOT ERROR</span>";
    const ERROR =  "<span style=\"color:red\">ERROR</span>";
    const SUCCESS =  "<span style=\"color:red\">SUCESS</span>";
    const FAILURE =  "<span style=\"color:red\">FAILURE</span>";

    # configuration du jeu de caractere
    private static $charset = null;

    # mail
    public static mail = null;

    # Le mode de recuperation de des
    private static $modeDeRecuperationDeDonnee = \PDO::FETCH_OBJ;

    # Object de connection PDO
    private static $bdd = null;

    # Fetch Mode
    const OBJECT = \PDO::FETCH_OBJ;
    const ASSOC = \PDO::FETCH_ASSOC;
    const NUM = \PDO::FETCH_NUM;

    # repertoire par defaut d'upload de fichier
    private static $uploadDir = "uploaded";

    # taille du fichier
    private static $fileSize = 2000000;

    /**
    * initialisation de la chaine de connection
    * e.g mysql://username:password@hostname:port/dbname
    */

    public function __construct()
    {
        self::$mail = new DooMaili();
    }

    public static function init($cb = null)
    {

        self::$bdd = Doodb::connection($cb);

        if(self::$bdd !== null)
        {

            if(self::$charset !== null)
            {

                self::$bdd->exec("SET NAMES " . self::$charset);

            }

        }

    }

    /**
    * setCharset, permet de reinitialiser le jeux de caracter
    * @param strting: encodage
    * @return mixed
    */

    public static function setCharset($charset, $cb = null)
    {

        if(!in_array($charset, ["UTF8", "ISO-8859"])){

            if($cb !== null)
            {

                return call_user_func_array($cb, [new \Exception('Encodage non valide')]);

            }
            else
            {

                trigger_error("Encodage non valide", E_USER_WARNING);
                exit();

            }

        }
        else
        {



        }

        self::$charset = $charset;

        if($cb !== null)
        {

            call_user_func($cb, null);

        }

    }
    /**
    * setFetchMode, fonction permettant de redefinir la methode de recuperation des information
    * @param int: PDO fecth constant
    */
    public static function setFetchMode($pdoFetchMode)
    {

        self::$modeDeRecuperationDeDonnee = (int) $pdoFetchMode;

    }

    /**
    * select: fonction permettant d'executer tout forme de requette de type SELECT
    *
    * @param string table : la table sur laquelle faire la selection
    * @param array fields : liste de colonne à visualiser
    * @param function cb: fonction de recuperation des erreurs et des donnees
    * @param array [where = null]: condition supplementaire
    * @param boolean order: ORDER BY
    * @param string limit
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
        if($where !== null && is_array($where))
        {

            $c = count($where);

            if($c == 1)
            {

                $query .= " WHERE " . implode(",", $where);

            }
            else if($c == 2)
            {

                if(is_array($order) && count($order) == 2)
                {

                    trigger_error("Syntax error, le parametre apres la fonction est un where de ", E_WARNING);
                    exit();

                }
                else if(is_string($order))
                {
                    $limit = $order;
                }

            }
            else
            {
                if(is_string($order))
                {

                    $limit = $order;
                    $order = $where;

                }
            }

        }
        elseif(is_string($where))
        {

            $limit = $where;

        }

        if(is_array($order) && count($order) == 2)
        {

            if(end($order) === true)
            {

                $query .= " ORDER BY " . $order[0] . " DESC";

            }
            else
            {

                $query .= " ORDER BY " . $order[0] . " ASC";

            }

        }
        elseif(is_string($order))
        {

            $limit = $order;

        }

        if($limit !== null)
        {

            $query .= " LIMIT " . $limit;

        }

        if(self::$charset !== null)
        {

            self::$bdd->exec("SET NAMES " . self::$charset);

        }

        $req = self::$bdd->query($query);


        $err = self::getError(self::$bdd->errorInfo(), $query);

        if(is_bool($req))
        {

            $cb($err, []);

        }else
        {

            $cb($err, $req->fetchAll(self::$modeDeRecuperationDeDonnee));

        }

    }

    /**
    * update: fonction permettant d'executer tout forme de requette de type UPDATE
    * @param string table: la table sur laquelle faire la selection
    * @param array fields: liste de colonne à mettre a jour
    * @param function cb: fonction de recuperation des erreurs et des donnees
    * @param string where: where condition
    */

    public static function update($table, $fields, $where, $cb)
    {

        $query = "UPDATE " . $table . " SET " . implode(", ", $fields) . " WHERE " . $where;

        self::$bdd->exec($query);

        $cb(self::getError(self::$bdd->errorInfo(), $query));

    }

    /**
    * insert: fonction permettant d'executer tout forme de requette de type INSERT
    * @param string table : la table sur laquelle faire la selection
    * @param array fields: liste de colonne à inserer
    * @param function cb: fonction de recuperation des erreurs et des donnees
    */

    public static function insert($table, $fields, $cb)
    {

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
    * @param string table
    * @param array where: id des colonnes à supprimer
    * @param function cb: fonction de recuperation des erreurs et des donnees
    */

    public static function delete($table, $where, $cb = null)
    {

        $query = "DELETE FROM " . $table . " WHERE id = :id";

        $req = self::$bdd->prepare($query);

        $req->bindValue(":id", $where["id"], \PDO::PARAM_INT);

        $req->execute();
        if($cb !== null)
        {
            call_user_func($cb, [self::getError(self::$bdd->errorInfo(), $query)]);
        }

    }

    /**
    * uploadFile, fonction permettant d'uploaded un fichier.
    *
    * @param array, un tableau comportant les inforamtio sur le fichier a uploader provenant de la variable $_FILES.
    * @param array, liste des extensions valides.
    * @param string[$uploadedDirectory = null], chemin du repretoire dans lequelle le fichier toi etre uploader.
    * @param fonction[$cb = null], fonction de rappel pour recuperer les erreurs.
    */

    public static function uploadFile($file, array $extension, $cb = null, $hash = null)
    {

        if(is_array($extension))
        {

            $extensionValide = $extension;

        }
        else
        {

            $cb = $extension;

        }

        # Si le fichier est bien dans le repertoir tmp de PHP
        if(is_uploaded_file($file["tmp_name"]))
        {

            if(!is_dir(self::$uploadDir))
            {

                mkdir(self::$uploadDir, 0777);

            }

            # Si le fichier est bien uploader, avec aucune error
            if($file["error"] === 0)
            {

                if($file["size"] <= self::$fileSize)
                {

                    $pathInfo = (object) pathinfo($file["name"]);

                    if(in_array($pathInfo->extension, $extensionValide))
                    {

                        if($hash !== null)
                        {

                            $filename = hash($hash, uniqid(rand(null, true)));

                        }else
                        {

                            $filename = $pathInfo->filename;

                        }

                        $ext = $pathInfo->extension;

                        move_uploaded_file($file["tmp_name"], self::$uploadDir . "/" . $filename . '.' . $ext);

                        # Status, fichier uploadé
                        $status = [
                            "status" => self::SUCCESS,
                            "message" => self::surround('File Uploaded.', '#6DD37C')
                        ];

                    }
                    else
                    {

                        # Status, extension du fichier
                        $status = [
                            'status' => self::FAILURE,
                            'message' => self::surround(self::FAILURE . ': Availabe File, verify file type.', '#E1371A')
                        ];

                    }

                }
                else
                {

                    # Status, la taille est invalide
                    $status = [
                        'status' => self::FAILURE,
                        'message' => self::surround('File is more big, max size ' . self::$fileSize. ' octets.', '#E1371A')
                    ];

                }

            }
            else
            {

                # Status, fichier erroné.
                $status = [
                    "status" => self::FAILURE,
                    "message" => self::surround('Le fichier possède des erreurs.', "#E1371A")
                ];

            }

        }
        else
        {

            # Status, fichier non uploadé
            $status = [
                "status" => self::FAILURE,
                "message" => self::FAILURE . ' : Le fichier n\'a pas pus être uploader.'
            ];

        }

        if($cb !== null)
        {

            call_user_func_array($cb, [(object) $status, isset($filename) ? $filename : null, isset($ext) ? $ext : null]);

        }

    }

    /**
    * setUploadedDir, fonction permettant de redefinir le repertoir d'upload
    * @param string:path, le chemin du dossier de l'upload
    */
    public static function setUploadedDir($path)
    {

        if(is_string($path))
        {

            self::$uploadDir = $path;

        }else
        {

            trigger_error("SVP, une chaine de caracter est demander.", E_WARNING);
            exit();

        }

    }

    /**
    * getError est une fonction permetant de formater des erreurs, et nous les envoyes
    *
    * @param array, liste des erreurs generer pas PDOException
    * @param string, la requete sur laquelle il y a eu l'erreur
    * @return object, un objet contenant les informations formates de l'erreur
    */
    private static function getError($err, $query)
    {

        return (object) [
            "error" => $err[2] !== null ? true: false,
            "query" => $query,
            "errorInfo" => $err[2] !== null ? self::ERROR .": ". str_replace("' ", " ", preg_replace("#'[a-zA-Z_-]+\.#", "", $err[2])) : self::NOTERROR
        ];

    }

    /**
    * surround, fonction permettant de formater en HTML un message d'error
    *
    * @param string, message.
    * @param string, color
    * @return string, message formater
    */
    private static function surround($message, $color)
    {

        return '<span style="color:' . $color . '">' . $message . '</span>';

    }

    /**
    * setFileSize, fonction permettant de modifer la taille des fichiers a uploader
    *
    * @param int, nouvelle taille des fichier a uploader
    */
    public static function setFileSize($fileSize)
    {

        self::$fileSize = (int) $fileSize;

    }

    public static function mail()
    {
        return new DooMaili();
    }

    public static function date($cfg = null) {
        if($cfg !== null)
        {

            return new DooDateMaker($cfg);

        }

        return new DooDateMaker($cfg);
    }

    # Fichier, contenant un code simple en php, nous permettant d'executer de requete SQL.
}
