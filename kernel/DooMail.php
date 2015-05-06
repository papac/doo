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
  public static $additionnalHeader = null;

  function __construct(argument)
  {
    # code...
  }
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

    return $cb('Good!');

  }

  public static function send($cb = null)
  {

    if(self::$additionnalHeader){
      $status = mail(self::$destination, self::$subject, self::$message, self::$additionnalHeader);
    }else{
      $status = mail(self::$destination, self::$subject, self::$message);
    }

    if($cb !== null)
    {
      $cb($status);
    }

  }

}


?>
