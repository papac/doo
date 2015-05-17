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

  Doo::init("mysql://root@localhost/test", function($err)
  {
      if($err) {
         die($err->getMessage());
      }
  });

  Doo::select("news", ["*"], function($err, $data)
  {
      if($err->error)
      {
          die($err->errorInfo);
      }
  });

