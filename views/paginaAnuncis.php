<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
        session_start(); 

        //inicialitzem Twig
        require_once '../Twig/autoloader.php';
        Twig_Autoloader::register();

        $loader = new Twig_Loader_Filesystem('../templates');
        $twig = new \Twig_Environment($loader,['cache'=>false]);

        //importem el json dels llibres
        $jsonAnuncis = file_get_contents('../api/anuncis.json');
        $anuncis = json_decode($jsonAnuncis);

        //si no hi ha cap array la inicialitzem buida
        if(!is_array($anuncis)){
            $anuncis=[];
        }


         /*renderitzem la plantilla. Dirigim a fitxer .twig de la pagina inicial
        enviant l'array del json books*/
        echo $twig->render('paginaAnuncis.html.twig',['anuncis' => $anuncis,'rol' => $_SESSION['rol']]);
    ?>
</body>
</html>