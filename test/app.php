<?php

    require __DIR__ ."/../kernel/Autoload.php";
    use \Doo\Autoload;
    use \Doo\Doo;


    Autoload::register();

    Doo::init(function($err){

        if($err !== null) {

            var_dump($err);

            die($err->getMessage());

        }

    });

    $g = new stdClass;

    foreach (["post", "slider"] as $key => $value) {

        Doo::select($value, ["*"], function($err, $data) use (&$g, $value)
        {
            if($err instanceof \Exception) {

                die($err->getMessage());

            } else if ($err->error) {

                die($err->errorInfo);

            }

            $g->{$value} = $data;

        });

    }

    var_dump($g);
