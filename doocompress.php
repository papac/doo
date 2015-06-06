<?php
	
	echo '<div style="width: 100%; word-break: break-all; font-family: \'courier new\'; font-size: 10px;">';

    define("APP_ROOT", str_replace("\\", "/", __DIR__) . "/kernel/");

    $dir = opendir(APP_ROOT);

    while ($file = readdir($dir)) {

        if(!is_dir(APP_ROOT . $file)) {

            echo "<div>${file} : <br/>" . base64_encode(file_get_contents(APP_ROOT . $file)) . "</div><br/>";
        }

    }

    closedir($dir);

    echo "<br/>artisan: <br/>" . base64_encode(file_get_contents("artisan"));

    echo "</div>";
