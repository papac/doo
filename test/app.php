<?php

  use Doo\Autoload;
  use Doo\Doodata;
  use Doo\DooMail;

  require '../Kernel/Autoload.php';

  Autoload::register();

  $data = null;

  Doodata::crypto('Dakia Franck Gilbert', function($res)
  {
  	global $data;

  	$data = $res;
  	var_dump($data);

  });

  Doodata::decrypto($data, function($res)
  {
  	var_dump($res);

  });


  DooMail::factory([
    'to' => 'dakiafranckinfo@gmail.com',
    'subject' => 'Coucou',
    'data' => 'je teste a present une classe sendmail'
  ], function($help)
  {
    die($help);
  });


  DooMail::send(function($err)
  {
    die($err);
  });

  Doo::init("mysql://root@localhost/diagnostic", function($err)
  {
    die($err);
  });

  Doo::select("users", ["id", "nom"], function($err, $data)
  {

    if($err->error)
    {
      echo $err->errorInfo;
      exit(null);
    }

  }, ["nom", false]);

  Doo::insert("users", ["id" => "i", "nom" => "Franck"], function($err)
  {
    if($err->error)
    {
      echo $err->errorInfo;
      exit();
    }
  });
