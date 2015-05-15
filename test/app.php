<?php

    use \Doo\Doo;
    use \Doo\Autoload;

    require "../kernel/Autoload.php";

    Autoload::register();

    Doo::init("mysql://root@localhost/test", function($err)
    {
        if($err instanceof \Exception)
        {
            die($err->getMessage());
        }
    });

    Doo::setFetchMode(Doo::NUM);

    Doo::select("news", ["auteur", "message"], function($err, $data)
    {
        if($err->error):
            echo $err->getMessage();
            return null;
        endif;
?>


    <?php foreach($data as $key => $value): ?>
        <?= $value[1] ?>
    <?php endforeach; ?>


<?php }); ?>
