<?php

    require "../kernel/Autoload.php";

    use \Doo\Autoload;
    use \Doo\Doo;

    Autoload::register();

    Doo::init(function($err) {
        if($err instanceof \Exception)
        {
            die($err->getMessage());
        }
    });

    Doo::select("post", ["*"], function($err, $data)
    {
        if($err->error)
        {
            die($err->errorInfo);
        }

        foreach($data as $k => $v){
            echo "{$v->id}: {$v->content}<br/>";
        }
        
    });
