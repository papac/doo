<?php

/**
* PHP, une classe PHP simple. Dans le but simplifié l'utilisation de PDO
* @author Dakia Franck <dakiafranckinfo@gmail.com>
* @package Doo;
* @version 0.1.0
*/

namespace Doo;

/**
* DooMail, classe permettant d'envoyer des mails
*/
class DooMaili
{

    const FORMAT = "[
        'to' => to@maildomain.com,
        'subject' => you subject,
        'data' => your data
    ]";

    private $to = null;
    private $from = null;
    private $subject = null;
    private $data = null;
    private $additionnalHeader = [];
    private $boundaryHash;

    function __construct()
    {

        $this->boundaryHash = md5(date("r", time()));

    }

    /**
    * factory, fonction permettant de construire le message a envoye
    * @param array, information du message.
    * @param function, fonction de rappel
    * @return string.
    */
    public function factory(array $information, $cb = null)
    {

        if(!is_array($information))
        {

            if($cb !== null)
            {

                return $cb($this->FORMAT);

            }

            return self::FORMAT;

        }

        $this->to = $information['to'];
        $this->subject = $information['subject'];
        $this->data = $information['data'];

        if($cb !== null)
        {

            $cb(self::FORMAT);

        }

        return $this;

    }

    /**
    * from, fonction permettant de definir l'envoyeur de mail
    * @param string
    * @param DooMaili, Object DooMaili
    */
    publuc function from($from)
    {
        if(is_string($from))
        {

            $this->from = $from;            

        }
        else
        {

             return self::errno();

        }

        return $this;
    }

    /**
    * to, fonction permetant de definir le destinateur de mail
    * @param string
    * @return DooMaili, Object DooMaili
    */
    public function to($to)
    {

        if(is_string($to))
        {
        
            $this->$to = $to;
        
        }
        else
        {

            self::errno("Excepted parameter string.");

        }
        
        return $this;

    }

    /**
    * subject, fonction permetant de definir le sujet du mail
    * @param string
    * @return DooMaili, Object DooMaili
    */
    public function subject($sub){

        if(is_string($sub))
        {

            $this->subject = $sub;
            
        }
        else
        {

            self::errno("Excepted parameter string.");

        }

        return $this;

    }

    /**
    * data, fonction permettant de definir le message a envoyer
    * @param string
    * @return DooMaili, Object DooMaili
    */
    public function data($msg)
    {

        if(is_string($msg))
        {

            $this->data = $msg;
            
        }
        else
        {

            self::errno("Excepted parameter string.");

        }

        return $this;

    }

    /**
    * addHeader, fonction permettant d'ajouter des headers suplementaire.
    * @param array, un tableau comportant les headers du mail
    */
    public function addHeader($head)
    {

        $this->additionnalHeader[] = $head;

    }

    /**
    * send, fonction de declanchement de l'envoie de mail
    * @param function, fonction de rappel pour recuperer l'etat apres envoie de mail
    */
    public function send($cb = null)
    {

        if($this->additionnalHeader !== null){

            $status = @mail($this->to, $this->subject, $this->data, $this->additionnalHeader);

        }else{

            $status = @mail($this->to, $this->subject, $this->data);

        }

        if($cb !== null)
        {

            $cb($status);

        }

    }

    /**
    * setMailServer, fonction permettant de definir de quel serveur le mail sera envoyer
    * @param string, le nom de serveur, ou l'adresse IP du serveur
    */
    public function setMailServer($serverName)
    {

        if(is_string($serverName))
        {
            ini_set('SMTP', $serverName);
        }

        return $this;

    }

    /**
    * setPort, fonction permettant de configurer le port smtp
    * @param string, le numero de port
    */
    public function setPort($port)
    {

        if(is_string($port))
        {

            ini_set('smtp_port', $port);

        }

        return $this;

    }
    /**
    * errno, fonction permettant de 
    */
    private static function errno($msg)
    {
        trigger_error($msg, E_WARNING);
        exit();
    }

    private function prepareAttachement()
    {

        
        
    }

}