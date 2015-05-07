<?php

  use Doo\Autoload;
  use Doo\DooDateMaker;

  require '../Kernel/Autoload.php';

  Autoload::register();

  $dateMaker = new DooDateMaker('ci_CI');
  var_dump($dateMaker->hours() . ":" . $dateMaker->minutes() . ":" . $dateMaker->seconds());
