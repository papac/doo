<?php

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
    private $subject = null;
    private $data = null;
    private $additionnalHeader = null;


    public function factory(array $information, $cb = null)
    {

        if(!is_array($information))
        {

            if($cb !== null)
            {

                return $cb($this->FORMAT);

            }

            return $this->FORMAT;

        }

        $this->to = $information['to'];
        $this->subject = $information['subject'];
        $this->data = $information['data'];

        if($cb !== null)
        {

            $cb($this->FORMAT);

        }

        return $this;

    }

    public function to($to)
    {

        $this->$to = $to;
        return $this;

    }

    public function form($form)
    {

        $this->to = $form;
        return $this;

    }

    public function subject($sub){

        $this->subject = $sub;
        return $this;

    }

    public function data($msg)
    {

        $this->data = $msg;
        return $this;

    }

    /**
    * addHeader, fonction permettant d'ajouter des headers suplementaire.
    * @param array, un tableau comportant les headers du mail
    */
    public function addHeader(array $heads, $cb = null)
    {

        if(is_array($heads))
        {

            $this->$additionnalHeader = '';

            $i = 0;

            foreach($heads as $key => $value)
            {

                $this->$additionnalHeader .= $key . ":" . $value . ($i > 0 ? ", ": "");
                $i++;

            }

        }

        if($cb !== null)
        {

            $cb($this->$additionnalHeader);

        }

    }

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

    public function setMailServer($serverName)
    {

        if(is_string($serverName))
        {
            ini_set('SMTP', $serverName);
        }

        return $this;

    }

    public function setPort($port)
    {

        if(is_string($port))
        {

            ini_set('smtp_port', $port);

        }

        return $this;

    }

}