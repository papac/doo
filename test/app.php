<?php

    require __DIR__ ."/../kernel/Autoload.php";
    use \Doo\Autoload;
    use \Doo\Doo;


    Autoload::register();
    Doo::init("mysql://root@localhost/web", function($err){
        if($err !== null) {
            var_dump($err);
            die($err->getMessage());
        }
    });

    $g = [];
    Doo::select("post", ["*"], function($err, $data) use (&$g)
    {
        if($err instanceof \Exception) {
            die($err->getMessage());
        } else if ($err->error) {
            die($err->errorInfo);
        }

        $g["post"] = $data;

    });

    Doo::select("slider", ["*"], function($err, $data) use (&$g)
    {
        if($err instanceof \Exception) {
            die($err->getMessage());
        } else if ($err->error) {
            die($err->errorInfo);
        }

        $g["slider"] = $data;

    });

    var_dump($g);