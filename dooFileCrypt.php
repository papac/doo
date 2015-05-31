<?php

    define("APP_ROOT", str_replace("\\", "/", __DIR__) . "/kernel/");

    $dir = opendir(APP_ROOT);

    while ($file = readdir($dir)) {

        if(!is_dir(APP_ROOT . $file)) {

            echo "${file} : " . base64_encode(file_get_contents(APP_ROOT . $file)) . "\n";
        }

    }

    closedir($dir);
