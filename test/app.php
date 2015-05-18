<?php

    use \Doo\Doo;
              use \Doo\Autoload;

    require "../kernel/Autoload.php";
    require "../Mail-master/mail.php";

    $mail = new Mail("dakia", "franck", "cucu", "je suis", "<p>je suis</p>");

    var_dump($mail);

    Autoload::register();

    Doo::init("mysql://root@localhost/test", function($err)
    {
        if ($err instanceof \Exception) {
            die($err->getMessage());
        }
    });

    Doo::select("news", ["*"], function($err, $data)
    {
        if ($err->error) {
            die($err->errorInfo);
        }

        var_dump($data);
    });

    $mail = new \Doo\DooMaili();
    $mail->setTypeMime("text/html");
    $mail->to("test@gmail.com")->subject("coucou")->data("je suis au quartier")->send(function($status) {

        if ($status) {
            echo "Mail envoyer.";
        } else {
            echo "Mail non envoyer.";
        }

    });

    var_dump("");

