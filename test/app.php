<?php

  require "../Kernel/Autoload.php";

  \Doo\Autoload::register();

  $message = <<< EOPAGE

    Lorem ipsum dolor sit amet,
    consectetur adipisicing elit, sed do eiusmod
    tempor incididunt ut labore et dolore magna aliqua.
    Ut enim ad minim veniam, quis nostrud exercitation
    ullamco laboris nisi ut aliquip ex ea commodo consequat.
    Duis aute irure dolor in reprehenderit in voluptate
    velit esse cillum dolore eu fugiat nulla pariatur.
    Excepteur sint occaecat cupidatat non proident,
    sunt in culpa qui officia deserunt
    mollit anim id est laborum.

EOPAGE;

  $sub = "Je suis la mon type";
  $des = "dakiafranckinfo@gmail.com";
  $server = "smtp.gmail.com";
  $port = "587";

  $mailer = new \Doo\DooMaili();

  $statusMessage = null;

  $mailer
    ->setMailServer($server)
    ->setPort($port)
    ->to($des)
    ->subject($sub)
    ->data($message)
    ->send(function($status)
    {
      global $statusMessage;

      if(!$status)
      {
        return $statusMessage = "Mail non envoyé.";
      }

      return $statusMessage = "Mail bien envoyé.";

    });

$info = <<< EOPAGE
<div style="border:1px solid #444; padding: 10px; font-size: 18px; color: #AAA;">
  ${statusMessage}
</div>

EOPAGE;

echo $info;
