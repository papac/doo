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
class Doo extends DooData{

    /**
    * Super constante, permetant de simplifier le information sur les erreurs
    */
    const ERROR =  3;
    const SUCCESS =  2;
    const WARNING = 1;

    # configuration du jeu de caractere
    private static $charset = null;

    # mail
    public static $mail = null;

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

    # mail sigleton
    private static $mail = null;

    /**
    * initialisation de la chaine de connection
    * e.g mysql://username:password@hostname:port/dbname
    */

    public function __construct()
    {
        self::$mail = new Doomail();
    }

    public static function init($dsn = null, $cb = null)
    {

        if($dsn !== null)
        {
            if(!is_string($dsn))
            {
                $cb = $dsn;
            }
            else
            {
                if($cb !== null)
                {

                    self::$bdd = Doo::connection($dsn, $cb);

                }
                else
                {
                    self::$bdd = Doodb::connection($cb);
                }
            }
        }


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
    * @param null cb
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

                self::doException($cb, "Encodage non valide", self::WARNING);
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
    * @throws \Exception
    */

    public static function select($table, $fields, $cb, $where = null, $order = false, $limit = null)
    {

        self::doException($cb, "Executez en premier cette commande, Doo::init(dsn, cb). ");

        # Une chaine contenant un liste de colonne des elements en visualiser
        $fields = implode(", ", $fields);

        # Statement par defaut
        $query = "SELECT " . $fields . " FROM " . $table;


        # Construction d'un SQL statement personnalisable
        if($where !== null && is_array($where))
        {

            $c = count($where);

            if($c == 1) {

                $query .= " WHERE " . implode(",", $where);

            } else if($c == 2) {

                if(is_array($order) && count($order) == 2) {
                    # Emission d'erreur
                    self::doException($cb, "Syntax error, le parametre apres la fonction est un where de ");

                }
                else if(is_string($order)) {

                    $limit = $order;

                }

            } else {

                if(is_string($order)) {

                    $limit = $order;
                    $order = $where;

                }
            }

        } else if(is_string($where)) {

            $limit = $where;

        }

        if(is_array($order) && count($order) == 2) {

            $query .= " ORDER BY " . $order[0];

            if(end($order) === true) {

                 $query .= " DESC";

            } else {

                $query .= " ASC";

            }

        } elseif(is_string($order)) {

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

            call_user_func_array($cb , [$err, []]);

        }else
        {

            call_user_func_array($cb, [$err, $req->fetchAll(self::$modeDeRecuperationDeDonnee)]);

        }

    }

    /**
    * update: fonction permettant d'executer tout forme de requette de type UPDATE
    * @param string table: la table sur laquelle faire la selection
    * @param array fields: liste de colonne à mettre a jour
    * @param function cb: fonction de recuperation des erreurs et des donnees
    * @param string where: where condition, { id = 2 and }
    */

    public static function update($table, $fields, $cb == null, $where = null)
    {
        self::doException($cb, "Executez en premier cette fonction, Doo::init(dsn, cb). <br/> ou verifiez cette fonction.");

        if($cb !== null) {

            if(is_string($cb)) {

                $where = $cb;
                $cb = null;

            }
        }

        $field = '';
        $i = 0;

        foreach($fields as $key => $value) {

            if(is_string($value)) {
                $value = "'{$value}'";
            }

            $field .= ($i > 0 ? ", " : "") .  "{$key} = {$value}";
            $i++;

        }

        $query = "UPDATE " . $table . " SET " . $field;

        if(is_string($where)) {

            $query .= " WHERE " . $where;

        }

        self::$bdd->exec($query);

        if($cb !== null) {

            call_user_func($cb, self::getError(self::$bdd->errorInfo(), $query));

        }

    }

    /**
    * insert: fonction permettant d'executer tout forme de requette de type INSERT
    * @param string table : la table sur laquelle faire la selection
    * @param array fields: liste de colonne à inserer
    * @param function cb: fonction de recuperation des erreurs et des donnees
    */

    public static function insert($table, $fields, $cb)
    {

        self::doException($cb, "Executez en premier cette commande, Doo::init(dsn, cb). ");

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

    public static function delete($table, $cb = null, $where = null)
    {

        doException($cb, "Executez en premier cette commande, Doo::init(dsn, cb). ");

        $query = "DELETE FROM " . $table . " WHERE";

        $i = 0;

        if($where === null) {

            self::doException($cb, "Syntaxe error", self::ERROR);

        }

        foreach ($where as $key => $value) {
            $query .= (i > 0 ? ' AND' : '') . " ${key} = :{$key}";
            $i++;
        }

        $req = self::$bdd->prepare($query);

        foreach ($where as $key => $value) {
            $req->bindValue("{$key}", $value, is_string($value) ? PDO::PARAM_STR : PDO::PARAM_INT);
        }

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
        $file = (object) $file;
        if(is_array($extension))
        {

            $extensionValide = $extension;

        }
        else
        {

            $cb = $extension;

        }

        # Si le fichier est bien dans le repertoir tmp de PHP
        if(is_uploaded_file($file->tmp_name))
        {

            if(!is_dir(self::$uploadDir))
            {

                mkdir(self::$uploadDir, 0777);

            }

            # Si le fichier est bien uploader, avec aucune error
            if($file->error === 0)
            {

                if($file->size <= self::$fileSize)
                {

                    $pathInfo = (object) pathinfo($file->name);

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

                        move_uploaded_file($file->tmp_name, self::$uploadDir . "/" . $filename . '.' . $ext);

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
                            'status' => self::ERROR,
                            'message' => self::surround('Availabe File, verify file type.', self::ERROR)
                        ];

                    }

                }
                else
                {

                    # Status, la taille est invalide
                    $status = [
                        'status' => self::ERROR,
                        'message' => self::surround('File is more big, max size ' . self::$fileSize. ' octets.', self::ERROR)
                    ];

                }

            }
            else
            {

                # Status, fichier erroné.
                $status = [
                    "status" => self::ERROR,
                    "message" => self::surround('Le fichier possède des erreurs.', self::ERROR)
                ];

            }

        }
        else
        {

            # Status, fichier non uploadé
            $status = [
                "status" => self::ERROR,
                "message" => self::suround(' : Le fichier n\'a pas pus être uploader.', self::ERROR)
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

        if(is_string($path)) {

            self::$uploadDir = $path;

        } else {

            self::doException(null, "SVP, une chaine de caracter est demander.");
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

        $orign = preg_replace("#'[a-zA-Z_-]+\.#", "", $err[2]);

        return (object) [
            "error" => $err[2] !== null ? true: false,
            "query" => $query,
            "errorInfo" => $err[2] !== null
                            ? self::surround(str_replace("' ", " ", $orign), self::ERROR)
                            : self::SUCCESS
        ];

    }

    /**
    * surround, fonction permettant de formater en HTML un message d'error
    *
    * @param string $message.
    * @param int, $errCode
    * @return string $message, formater
    */
    private static function surround($message, $errCode = null)
    {

        if($errCode === self::ERROR) {

            $color = "#FF5B60";

        } else if($errCode === self::SUCCESS) {

            $color = "#8BFF88";

        } else {

            $color = "#F7CF59";

        }

        return '<span style="font-family: verdana; font-size: 15px; background-color:' . $color . '; color: white; display: block; padding: 10px; border-radius: 5px;">' . $message . '</span>';

    }

    /**
    * setFileSize, fonction permettant de modifer la taille des fichiers a uploader
    *
    * @param int, nouvelle taille des fichier a uploader
    */
    public static function setFileSize($fileSize) {

        self::$fileSize = (int) $fileSize;

    }

    /**
     * mail, fonction permettant d'initialiser la fonctionnalité envoye-mail du systeme
     *
     * @return \Doo\DooMaili
     */
    public static function mail() {

        if(self$mail === null) {

            self::$mail new Doomail();

        }

        return self::$mail;

    }

    /**
     * date, fonction de recuperation de la date
     *
     * @param null $cfg
     * @return \Doo\DooDateMaker
     */
    public static function date($timestanp = null) {

        if($timestanp !== null) {

            return new DoodateMaker($timestanp);

        }

        return new DoodateMaker();

    }

    private static function doException($cb = null, $message) {

        if(self::$bdd === null) {

            $err = new \Exception(self::surround($message, isset(func_get_args()[2]) ? func_get_args()[2] : self::ERROR));

            if($cb !== null)
            {
                call_user_func_array($cb, [$err, null]);
                die();
            }

            throw $err;
        }

    }

}
t
