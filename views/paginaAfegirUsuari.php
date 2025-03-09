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

        //inicalitzem PHPMailer
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
        require '../config/BBDD_connection.php'; 
        require '../models/usuaris.php'; 
    
    
        //Conectem amb la base de dades
        $conexio->exec("USE MP07_UF2_ProyecteFinal");


        $method = $_SERVER['REQUEST_METHOD'];

        //si el metode es posts creem un nou registre a la bbdd
        if ($method=='POST' && isset($_POST['crearNou']))
        {
            // Rebem l'informació de form-data ($_POST)
            $usuari = isset($_POST["user"]) ? $_POST["user"] : "";
            $email = isset($_POST["mail"]) ? $_POST["mail"] : "";
            $password = isset($_POST["password"]) ? $_POST["password"] : "";
            $rol = isset($_POST["rol"]) ? $_POST["rol"] : "";    
            

            //criptem la contrasenya
            $crypEmail=password_hash($password, PASSWORD_DEFAULT);
            $newUser = new Usuari ( $usuari, $email, $crypEmail,  $rol);
            try
            {
                //evitem que puguin fer inyection SQL
                $newRegistre = $conexio->prepare("INSERT INTO usuaris (usuari,email,contrasenya,rol) VALUES (:usuari,:email,:crypEmail,:rol)");
                $newRegistre -> bindparam(":usuari",$newUser->nom,PDO::PARAM_STR); 
                $newRegistre -> bindparam(":email",$newUser->email,PDO::PARAM_STR); 
                $newRegistre -> bindparam(":crypEmail",$newUser->contrasenya,PDO::PARAM_STR); 
                $newRegistre -> bindparam(":rol",$newUser->rol,PDO::PARAM_STR); 
                $newRegistre->execute();

                //registrem missatge de usuari creat correctament
                $_SESSION['message'] ="Usuari creat correctament";
            }
            catch(Exception $ex)
            {
                error_log("Error de inserción en la base de datos: " . $e->getMessage(), 3, "../Error/error.log");
            }
    
            if(filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                try {
    
                    // Configuració del servidor SMTP
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = "smtp.gmail.com"; 
                    $mail->SMTPAuth = true;
                    $mail->Username = "natgsarabia@gmail.com"; 
    
                    //he creat una contraseña per la applicació de PHPMailer
                    $mail->Password = "uals rczy hgby axyu";    
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
    
                    $mail->setFrom("natgsarabia@gmail.com",$usuari);
                    $mail->addAddress("natgsarabia@gmail.com");
    
                    $mail->Subject="Nou usuari creat $usuari";
                    $mail->Body="L'usuari $usuari té el rol $rol y al mail $email";
                    
    
                    $mail->send();
                
                }
                catch (Exception $ex)
                {
                    error_log("Error d'enviament del mail: " . $mail->ErrorInfo, 3, "../Error/error.log");
                }
            }
            else
            {
                error_log( "Correu invalit"->ErrorInfo, 3, "../Error/error.log");
            } 
        }

        //Definim acció per eliminar registre
        else if($method=='POST' && isset($_POST['delete']))
        {
            //ELIMINEM REGISTRE:
            // Rebem l'informació de form-data ($_POST)
            $id = isset($_POST["delete_id"]) ? $_POST["delete_id"] : "";
            $id = intval($id);

            //busquem l'informacio de l'usuari que eliminarem per pasar-la posteriorment com a variables
            //per enviar el mail
            $searchRegistre = $conexio->prepare("SELECT usuari, email,rol FROM usuaris WHERE  id = :id");
            $searchRegistre -> bindparam(":id",$id,PDO::PARAM_INT); 
            $searchRegistre->execute();
            $resultData = $searchRegistre->fetch(PDO::FETCH_ASSOC);

            //guardem els valors en variables
            $usuari=$resultData["usuari"];
            $email=$resultData["email"];
            $rol=$resultData["rol"];

            $eliminarRegistre =$conexio -> prepare("DELETE  from usuaris where id=:id");
            $eliminarRegistre->bindparam(":id",$id,PDO::PARAM_INT); 
            $eliminarRegistre->execute();

            //registrem missatge de usuari eliminat correctament
            $_SESSION['message'] ="Usuari eliminat correctament";


            //ENVIEM MAIL COMFIRMANT ELIMINACIÓ
            if(filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                try {
    
                    // Configuració del servidor SMTP
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = "smtp.gmail.com"; 
                    $mail->SMTPAuth = true;
                    $mail->Username = "natgsarabia@gmail.com"; 
    
                    //he creat una contraseña per la applicació de PHPMailer
                    $mail->Password = "uals rczy hgby axyu";    
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
    
                    $mail->setFrom("natgsarabia@gmail.com",$usuari);
                    $mail->addAddress("natgsarabia@gmail.com");
    
                    $mail->Subject="Usuari $usuari eliminat";
                    $mail->Body="S'ha eliminat correctament l'usuari $usuari amb el rol $rol y  mail $email";
                    
    
                    $mail->send();
                
                }
                //en cas d'error en l'enviament del mail creem un registre en el fitxer error.log
                catch (Exception $ex)
                {
                    error_log("Error d'enviament del mail: "->ErrorInfo, 3, "../Error/error.log");
                }
            }
            else
            {
                error_log( "Correu invalit"->ErrorInfo, 3, "../Error/error.log");
            } 
        }
        
        // sempre, quan entrem a la pagina, exportem tots el usuaris de la BBDD, per omplir la taula de registres
        $usuaris=[];
        $usuariData = $conexio->prepare("SELECT * FROM usuaris");
        $usuariData->execute();
        $usuaris=$usuariData->fetchAll(PDO::FETCH_ASSOC);
        
    
        $conexio = null;

        //mostrem missatge per pantalla en cas de creació/eliminació d'un registre
        if(isset($_SESSION['message']) && $_SESSION['message']!="")
        {
            //deixem el missatge vuit y li pasem totes les variables necessaries a la pagina de renderitzat
            $missatge=$_SESSION['message'];
            $_SESSION['message']=null;
            echo $twig->render('paginaAfegirUsuari.html.twig',['usuaris' => $usuaris,'rol' => $_SESSION['rol'], 'missatge'=>$missatge]);
        }
        else
        {
            echo $twig->render('paginaAfegirUsuari.html.twig',['usuaris' => $usuaris,'rol' => $_SESSION['rol']]);
        }
    ?>
    
</body>
</html>