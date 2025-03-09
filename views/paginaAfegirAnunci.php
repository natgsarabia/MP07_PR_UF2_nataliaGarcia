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

        //inicialitzem PHPMailer
        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\Exception;

        
        //Inicialitzem Twig
        require_once '../Twig/autoloader.php';
        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem('../templates');
        $twig = new \Twig_Environment($loader,['cache'=>false]);

        //Inicialitzem vendor
        require __DIR__ . '/../vendor/autoload.php';

        //Importem tots els moduls necesaris
        require '../models/anuncis.php'; 
        require '../config/BBDD_connection.php'; 
    
        //importem el json dels anuncis
        $jsonAnuncis = file_get_contents('../api/anuncis.json');
        $anuncis = json_decode($jsonAnuncis,true);
        
        //si no hi ha cap array la inicialitzem buida
        if(!is_array($anuncis)){
            $anuncis=[];
        }

        //En cas de metode de crear nou usari:
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crearNou'])) {
            $totalanuncisActuals=count($anuncis);

            //guardem totes les dades que venen en el POST del formulari en variables:
            $id=$totalanuncisActuals+1;
            $nom=$_POST['nom'];
            $descripcio=$_POST['descripcio'];
            $preu=intval($_POST['preu']);
            $categoria=$_POST['categoria'];
            $imgProducte=$_POST['imgProducte'];
            $id_usuari=intval($_SESSION['id_usuari']);
            
            //inicialitzem una variable clase anunci amb les dades rebudes
            $newAnunci = new Anunci($id, $nom, $descripcio, $preu, $categoria, $imgProducte, $id_usuari);

            //afegim el nou producte a la llista, ho fem d'aquesta forma peque tingui la mateixa estructura que el json
            $anuncis[] =  [
                "id" => $newAnunci->id,
                "nom" => $newAnunci->nom,
                "descripcio" => $newAnunci->descripcio,
                "preu" => $newAnunci->preu,
                "categoria" => $newAnunci->categoria,
                "imgProducte" => $newAnunci->imgProducte,
                "id_usuari" => $newAnunci->usuari_id,
                "data_registre" => $newAnunci->data_registre
            ];
            
            //I per últim guardem el fitxer json l'informació actualitzada
            file_put_contents('../api/anuncis.json',json_encode($anuncis,JSON_PRETTY_PRINT));
            
            //Repliquem el registre a BBDD:
            postAnunciBBDD ($newAnunci);
            
        }

        //Definim acció per eliminar registre
        else if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete']))
        {
            // Rebem l'informació de form-data ($_POST)
            $id = isset($_POST["delete_id"]) ? $_POST["delete_id"] : "";
            $id = intval($id);

            //creem un nou array sense el producte que tingui aquest ID
            $totalanuncisActuals=count($anuncis);

            //aquest nou array filtrem guardan tots el productes que no cumpleixin la condició
            $anuncis=array_values(array_filter($anuncis,function($product) use ($id){
                return intval($product['id']) !== $id;
            }));

            //asegurem que el producte s'ha borrat correctament
            if(count($anuncis)===$totalanuncisActuals)
            {
                error_log("EL producte no s'ha pogut borrar", 3, "../Error/error.log");
            }
            else
            {
                //en cas contrari actualitzem l'arxiu json
                file_put_contents('../api/anuncis.json',json_encode($anuncis, JSON_PRETTY_PRINT));
            }

            //Repliquem l'eliminació del producte a BBDD, per tenir sempre tot actualitzat
            deleteAnunciBBDD ($id);

        }
        
        //mostrem missatge per pantalla en cas de creació/eliminació d'un registre 
        if(isset($_SESSION['message']) && $_SESSION['message']!="")
        {
            //deixem el missatge vuit y li pasem la variable quan renderitzem la plantilla
            $missatge=$_SESSION['message'];
            $_SESSION['message']=null;
            echo $twig->render('paginaAfegirAnuncis.html.twig',['anuncis' => $anuncis,'rol' => $_SESSION['rol'], 'missatge'=>$missatge]);
        }
        else
        {
            echo $twig->render('paginaAfegirAnuncis.html.twig',['anuncis' => $anuncis,'rol' => $_SESSION['rol']]);
        }

    
    
        //funció per afeguir el producte també a la BBDD, y així tenir totes dues actualitzades
        function postAnunciBBDD ($newAnunci)
        {   
            global $conexio;
        
            $conexio->exec("USE MP07_UF2_ProyecteFinal");

            
            //evitem que puguin fer inyection SQL
            $newRegistre = $conexio->prepare("INSERT INTO anuncis (id,nom,descripcio,preu,categoria,imgProducte,id_usuari,data_registre) VALUES (:id,:nom,:descripcio,:preu,:categoria,:imgProducte,:id_usuari,:data_registre)");
            $newRegistre -> bindparam(":id",$newAnunci->id,PDO::PARAM_INT); 
            $newRegistre -> bindparam(":nom",$newAnunci->nom,PDO::PARAM_STR); 
            $newRegistre -> bindparam(":descripcio",$newAnunci->descripcio,PDO::PARAM_STR); 
            $newRegistre -> bindparam(":preu",$newAnunci->preu,PDO::PARAM_STR); 
            $newRegistre -> bindparam(":categoria",$newAnunci->categoria,PDO::PARAM_STR); 
            $newRegistre -> bindparam(":imgProducte",$newAnunci->imgProducte,PDO::PARAM_STR); 
            $newRegistre -> bindparam(":id_usuari",$newAnunci->usuari_id,PDO::PARAM_INT); 
            $newRegistre -> bindparam(":data_registre",$newAnunci->data_registre,PDO::PARAM_STR); 
            $newRegistre->execute();

    
             //registrem missatge de usuari eliminat correctament
             $_SESSION['message'] ="Anunci creat correctament";

            //tanquem la conexió amb BBDD
            $conexio = null;
        }

        //funció per afeguir el producte també a la BBDD, y així tenir totes dues actualitzades
        function deleteAnunciBBDD ($id)
        {   
            global $conexio;
    
            $conexio->exec("USE MP07_UF2_ProyecteFinal");
            
            //eliminem el registre
            $eliminarRegistre =$conexio -> prepare("DELETE  from anuncis where id=:id");
            $eliminarRegistre->bindparam(":id",$id,PDO::PARAM_INT); 
            $eliminarRegistre->execute();

            //registrem missatge de usuari eliminat correctament
            $_SESSION['message'] ="Anunci eliminat correctament";

            //tanquem la conexió amb BBDD
            $conexio = null;
        }
    ?>
</body>
</html>