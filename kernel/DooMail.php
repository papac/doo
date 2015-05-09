<?php

namespace Doo;

/**
* DooMail, classe permettant d'envoyer des mails
*/
class DooMail
{

    const FORMAT = "[
        'to' => destination@maildomain.com,
        'subject' => you subject,
        'data' => your message
    ]";

    private static $destination = null;
    private static $subject = null;
    private static $message = null;
    private static $additionnalHeader = null;
    private static $factoryIsDefine = false;

    /**
    * factory, fonction permettant de construire le message
    * [
    *   'to' => destination@maildomain.com,
    *   'subject' => you subject,
    *   'data' => your message
    * ].
    * @param array, les informations au formar montre ci-dessus.
    */
    public static function factory(array $information, $cb = null)
    {

        if(!is_array($information))
        {

            if($cb !== null)
            {

                return $cb(self::FORMAT);

            }

            return self::FORMAT;

        }

        self::$destination = $information['to'];
        self::$subject = $information['subject'];
        self::$message = $information['data'];

        if($cb !== null)
        {

            $cb(self::FORMAT);

        }

    }


    /**
    * addHeader, fonction permettant d'ajouter des headers suplementaire.
    * @param array, un tableau comportant les headers du mail
    */
    public static function addHeader(array $heads, $cb = null)
    {

        # Vérification de entete
        if(is_array($heads))
        {

            self::$additionnalHeader = '';

            $i = 0;

            foreach($heads as $key => $value)
            {

                # construction de la chaine d'entete.
                self::$additionnalHeader .= $key . ":" . $value . ($i > 0 ? ", ": "");
                $i++;

            }

        }

        # Vérification de la fonction de rappel
        if($cb !== null)
        {
            # Execution de la fonction de rappel
            $cb(self::$additionnalHeader);

        }

        self::factoryIsDefine = true;

    }

    /**
    * send,  fonction a executer apres l'execution du factory
    * @param function, fonction de rappel
    */
    public static function send($cb = null)
    {

        if(self::factoryIsDefine)
        {

            if($cb !== null)
            {

                $cb("Vous avez oublier de construir le message", self::FORMAT);
                return null;

            }
            else
            {

                trigger_error("Vous avez oublier de construir le message", E_USER_WARNING);

            }

        }

        if(self::$additionnalHeader)
        {

            $status = mail(self::$destination, self::$subject, self::$message, self::$additionnalHeader);

        }
        else
        {

            $status = mail(self::$destination, self::$subject, self::$message);

        }

        if($cb !== null)
        {
            $cb($status);
        }

    }

}
