<?php

  use Doo\Autoload;
  use Doo\DooDateMaker;

  require '../Kernel/Autoload.php';

  Autoload::register();

  $dateMaker = new DooDateMaker("es_ES");
  var_dump($dateMaker);

  var_dump($dateMaker->format('Y-m-d H:m:s', function($format)
  {
    $date = (array) date_create('Africa/Accra');
    var_dump($format, $date["date"]);
  }));

  var_dump(getdate());
