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

    /**
     *
     */
    const FORMAT = "<pre>[
        'to' => to@maildomain.com,
        'subject' => you subject,
        'data' => your data
    ]</pre>";

    /**
     * @var null
     */
    private $to = null;
    /**
     * @var null
     */
    private $from = null;
    /**
     * @var null
     */
    private $subject = null;
    /**
     * @var null
     */
    private $data = null;

    /**
     * @var array
     */
    private $additionnalHeader = [];

    /**
     * @var string
     */
    private $headers;

    /**
     * @var string
     */
    private $charset = 'iso-8896-1';
    /**
     * @var string
     */
    private $boundaryHash;

    /**
     * @var array
     */
    private $attachementFile;

    /**
     * @var string
     */
    private $typeMime;

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $html;

    function __construct()
    {

        $this->boundaryHash = md5(date("r", time()));
        $this->typeMime = "text/plain";
        $this->from = ini_get("smpt_from");

    }

    /**
     * setTypeMime, fonction de redefinir le type mime du message a envoye
     *
     * @param string $mime
     */
    public function setTypeMime($mime)
    {
        $this->typeMime = $mime;
    }

    /**
    * from, fonction permettant de definir l'envoyeur de mail
    *
    * @param string $from
    * @param DooMaili, Object DooMaili
    * @return $this
    */
    public function from($from)
    {
        if (is_string($from))
        {

            $this->from = $from;            

        }
        else
        {

             self::errno("From: <votre adress>@<domain>.<com>");

        }

        return $this;
    }

    /**
    * to, fonction permetant de definir le destinateur de mail
    *
    * @param string $to
    * @return DooMaili, Object DooMaili
    */
    public function to($to)
    {

        if (is_string($to))
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
    *
    * @param string $sub
    * @return DooMaili, Object DooMaili
    */
    public function subject($sub){

        if (is_string($sub))
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
    *
    * @param string $msg
    * @return DooMaili, Object DooMaili
    */
    public function data($msg)
    {

        if (is_string($msg))
        {

            $this->text = $msg;
            $this->dataMaker();

        }
        else
        {

            self::errno("Excepted parameter string.");

        }

        return $this;

    }

    /**
    * addHeader, fonction permettant d'ajouter des headers suplementaire.
    *
    * @param string $name, le nom du nouveau header
    * @param string $value, la valeur de nom enter
    * @return DooMaili $this
    */
    public function addHeader($name, $value)
    {

        $this->additionnalHeader[] = "{$name}: {$value}\r\n";
        return $this;

    }

    /**
     * addAttachementFile, fonction permettant d'ajouter une fichier
     *
     * @param string $file
     * @return $this
     */
    public function addAttachementFile($file)
    {
        if (is_string($file))
        {

            $this->attachementFile[] = $file;

        }

        return $this;

    }

    /**
     * setDefaultHeaders, fonction permettant de definir les headers par defaut
     */
    private function setDefaultHeaders()
    {

        $this->additionnalHeader[] = 'MIME-Version: 1.0';
        $this->additionnalHeader[] = "From: {$this->from}";
        $this->additionnalHeader[] = "To: {$this->to}";
        $this->additionnalHeader[] = "Subject: {$this->subject}";

        # We'll assume a multi-part message so that we can include an HTML and a text version of the email at the
        # very least. If there are attachments, we'll be doing the same thing.
        $this->additionnalHeader[] = "Content-type: multipart/mixed; boundary=\"PHP-mixed-{$this->boundaryHash}\"";

    }

    /**
     * text, fonction permettant de configurer les headers pour les text/plain
     */
    private function dataMaker()
    {

        $this->data .= "--PHP-alt-{$this->boundaryHash}\n";
        $this->data .= "Content-Type: {$this->typeMime}; charset=\"{$this->charset}\"\n";
        $this->data .= "Content-Transfer-Encoding: 7bit\n\n";
        $this->data .= $this->text."\n\n";

    }
    /**
    * send, fonction de declanchement de l'envoie de mail
    *
    * @param callable $cb, fonction de rappel pour recuperer l'etat apres envoie de mail
    */
    public function send($cb = null)
    {

        if (count($this->attachementFile) >= 1)
        {
            $this->prepareAttachementFile();
        }

        if (count($this->additionnalHeader) > 1)
        {

            $this->setDefaultHeaders();
            $this->headers = implode(PHP_EOL, $this->additionnalHeader).PHP_EOL;
            $status = @mail($this->to, $this->subject, $this->data, $this->headers);

        } else {

            $status = @mail($this->to, $this->subject, $this->data);

        }

        /** @var callable $cb */
        if ($cb !== null)
        {

            if (!empty($status)) {
                call_user_func($cb, $status);
            }

        }

    }

    /**
    * setMailServer, fonction permettant de definir de quel serveur le mail sera envoyer
    *
    * @param string $serverName, le nom de serveur, ou l'adresse IP du serveur
    * @return $this
    */
    public function setMailServer($serverName)
    {

        if (is_string($serverName))
        {

            ini_set('SMTP', $serverName);

        }
        else
        {

            self::errno("Excepted parameter string");

        }

        return $this;

    }

    /**
    * setPort, fonction permettant de configurer le port smtp
    *
    * @param string $port, le numero de port
    * @return $this
    */
    public function setPort($port)
    {

        ini_set('smtp_port', $port);
        return $this;

    }
    /**
    * errno, fonction permettant definir le message d'erreur.
    *
    * @param string, message
    */
    private static function errno($msg)
    {
        trigger_error($msg, E_WARNING);
        exit();
    }

    /**
     * prepareAttachement, fonction permettant d'ajouter une pièce-jointe
     */
    private function prepareAttachementFile()
    {
        foreach($this->attachementFile as $file)
        {

            $filename  = basename($file);
            $this->data .= "--PHP-mixed-{$this->boundaryHash}\n";
            $this->data .= "Content-Type: application/octet-stream; name=\"{$filename}\"\n";
            $this->data .= "Content-Transfer-Encoding: base64\n";
            $this->data .= "Content-Disposition: attachment\n\n";
            $this->data .= chunk_split(base64_encode(file_get_contents($file)));
            $this->data .= "\n\n";

        }

        $this->data .= "--PHP-mixed-{$this->boundaryHash}--\n\n";

    }

    /**
     * setCharset, fonction permettant de redefinir l'encodage
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

}