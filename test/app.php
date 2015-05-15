<?php

    use \Doo\Doo;
    use \Doo\Autoload;

    require "../kernel/Autoload.php";

    Autoload::register();

    Doo::setFileSize(400000);

    Doo::uploadFile($_FILES["file"], ["jpg", "png", "gif"], function($err)
    {

        echo $err->message;

    });