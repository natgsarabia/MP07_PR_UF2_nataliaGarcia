<?php

//Linea necesaria per que l'informació en format json es pugui interpretar correctament
header("Content-Type: application/json");

//declarem el métode utilitzat
$method = $_SERVER['REQUEST_METHOD'];
$file = 'anuncis.json';

// Leer el archivo JSON
$anuncis = file_exists($file) ? json_decode(file_get_contents($file), true) : [];


switch ($method) {
    case 'GET':
        try{
            //convertim l'array en format json
            echo json_encode($anuncis);
            break;
        }
        catch(Exception $ex)
        {
            error_log("Error al intentar obtenir els anuncis ", "../Error/error.log");
            break;
        }
    
    case 'POST':
        try{
            /*ACTUALITZEM EL JSON */
            //convertim en json l'array
            $input = json_decode(file_get_contents('php://input'),true);


            $ultimIndex=end($anuncis)['id'];

            //declarem el nou producte que agafarem del formulari client.php
            $nouAnunci=[
                //el id será un més de la suma total del productes
                "id"=>$ultimIndex+1,
                "name"=>$input['name'],
                "price"=>$input['price']
            ];

            //afegim el nou producte a la llista
            $anuncis[] = $nouAnunci; 
            
            //I per últim guardem el fitxer json l'informació actualitzada
            file_put_contents($file,json_encode($anuncis,JSON_PRETTY_PRINT));

            try
            {
                /*ACTUALITCEM LA BASE DE DADES */

                //Conectem amb la BBDD
                require '../config/BBDD_connection.php';
                //seleccionem la nova BBDD creada
                $conexio->exec("USE MP07_UF2_ProyecteFinal");
                

                //evitem que puguin fer inyection SQL
                $newRegistre = $conn->prepare("INSERT INTO contingut (titol,nom_grup,data_registre) VALUES (?, ?, ?)");
                $newRegistre -> bind_param("sss",$nouAnunci[],$nouAnunci[],$nouAnunci[]); 
                $newRegistre->execute();

                echo json_encode(["Anunci" => "Nou anunci creat correctament"]);

                $conn->close();
            }
            catch(Exception $ex)
            {
                error_log("Error al intentar crear l'anunci a la BBDD ".$nouanunci['name'], "../Error/error.log");
            }
        }
        catch(Exception $ex)
        {
            error_log("Error al intentar crear l'anunci al JSON ".$nouanunci['name'], "../Error/error.log");
        }
        break;
    
    default:
        //En cas de que s'intenti fer una acció no permesa actualitzem els headers e imprimim un misatge d'error
        header("HTTP/1.1 405 Method Not Allowed");
        error_log("Mètode no permès","../Error/error.log");
        echo json_encode(["error" => "Mètode no permès"]);
}
?>
