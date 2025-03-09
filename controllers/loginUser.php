<!-- Iniciem la sessió -->
<?php  
    session_start();  
    require '../config/BBDD_connection.php'; 

    // Recollim les variables enviadas al mètode POST 
    $usuari = $_POST['username'];
    $contrasenya = $_POST['password'];

    //incialitzem la sessio amb l'usuari y el nombre d'itents que ha fet per introduir la contrasenya
    $errors = $_SESSION['errors'] ?? 0;
    

    //confirmem la conexió
    if($conexio)
    {
        echo 'Conexió amb la base de dades realitzada correctament';
    }
    else
    {
        echo 'Error al conectar amb la BBDD: '.$conexio->error;
    }

    //Conectem amb la base de dades
    $conexio->exec("USE MP07_UF2_ProyecteFinal");
    echo "Seleccionada base de datos";

    //preparem query per obtenir la contrasenya y rol en cas de que existeixi l'usuari amb protecció per SQL Injeccion
    $query = "SELECT contrasenya,rol,id FROM usuaris WHERE usuari= :usuari";

    //preparem la consulta a MYSQL
    $prepareQuery = $conexio -> prepare($query);

    //Li passem per parametre el valor donat al formulari post, 's' indica que el parametre será de tipus string
    $prepareQuery ->bindparam(":usuari",$usuari, PDO::PARAM_STR);

    //executem la consulta
    $prepareQuery -> execute();

    //comprobem si s'han trobat resultats
    if($resultadoLogin = $prepareQuery -> fetch(PDO::FETCH_ASSOC)){

        /*en cas de que el registre l'usuari es trobi a la BBDD consultarem si la 
        contrasenya es correcte*/        
        if(password_verify($contrasenya,$resultadoLogin['contrasenya']))
        {
            /*En cas que la contrasenya sigui correcte, afegirem a la sessió el rol
            d'aquest usuari */
            $_SESSION['rol']=$resultadoLogin['rol'];
            $_SESSION['id_usuari']=$resultadoLogin['id'];

            echo "El rol es ", $_SESSION['rol'];
            
            //reiniciem els error a 0
            $_SESSION['errors']= 0; 

            

            if($_SESSION['rol']=="Administrador")
            {
                //si es un admin reenviem a la página de admins
                header("Location: ../views/paginaAdmin.php");
                exit();
            }
            else if($_SESSION['rol']=="Usuari")
            {
                //en cas de usuaris redirigim a usuari
                header("Location: ../views/paginaAnuncis.php");
                exit();
            }

        }
        else //en cas que la contraseña no sigui correcte y sumen un error
        {
            header("Location: ../public/index.php");
            $_SESSION['errors'] = $_SESSION['errors']+1;
            exit();
        }
        
    }
    else 
    {
        //en cas que l'usuari no existeixi redirigim a la pagina principal y sumem un error
        header("Location: ../public/index.php");
        $_SESSION['errors'] = $_SESSION['errors']+1;
        exit();
    } 

        //tanquem la sessió de MSQL
        $conexio = null;
?>