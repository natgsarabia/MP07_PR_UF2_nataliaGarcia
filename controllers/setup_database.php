<!-- Importem el ficher de conexió amb la BBDD -->
<?php
    require '../config/BBDD_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conexió i creació de taula BBDD</title>
</head>
<body>
    <h2>Conectem amb MySQL y creem la nova base de dades y les dues taules:</h2>
    <ol>
        <li><?php
            //confirmem la conexió
            if($conexio)
            {
                echo 'Conexió amb la base de dades realitzada correctament';
            }
            else
            {
                echo 'Error al conectar amb la BBDD: '.$conexio->error;
            }
        ?></li>

        <li><?php
            //creem la base de dades necesaria per l'exercici
            $createDatabase = "CREATE DATABASE IF NOT EXISTS MP07_UF2_ProyecteFinal";

            //comprobem si s'ha pogut crear correctament
            if($conexio -> query($createDatabase)){
                echo "Base de dades creada correctament <br>";
            }
            else 
            {
                echo "Error al crear BBDD<br>: ". $conexio->error."<br>";
            }

        ?></li>

        <li><?php
            //seleccionem la nova BBDD creada
            $conexio->exec("USE MP07_UF2_ProyecteFinal");
            echo "Seleccionada base de datos";
        ?></li>

        <li><?php
            //creem la tabla usuaris
            $createTableUser = "CREATE TABLE IF NOT EXISTS usuaris(
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuari VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(50) NOT NULL ,
                contrasenya VARCHAR(255) NOT NULL,
                rol ENUM ('Administrador','Usuari') NOT NULL)";
            
            //confirmem que s'ha creat correctament
            if($conexio -> query($createTableUser))
            {
                echo "Taula creada usuari correctament<br>";
            }
            else
            {
                echo "Error al crear la taula: <br>";
            }

            //creem contrasenyes per defecte xifrades per els posibles usuaris
            $passwordAdmin = password_hash("admin123", PASSWORD_DEFAULT);
            $passwordUser = password_hash("user123", PASSWORD_DEFAULT);

            //insertem a la taula dos registres de dos usuaris diferents
            $newQueryUsers = "
            INSERT INTO usuaris(usuari, email, contrasenya, rol)
            VALUES
            ('admin','admin@gmail.com','$passwordAdmin','Administrador'),('usuari','usuari@gmail.com','$passwordUser','Usuari')";

            //confirmem que els registres s'han creat correctament
            if($conexio -> query($newQueryUsers))
            {
                echo "Usuaris insertats correctament.<br>";
            }
            else
            {
                echo "Error al crear els usuaris:  ". $conexio->error."<br>";
            }

            //creem la tabla contingut
            $novaTaulaAnuncis = "CREATE TABLE IF NOT EXISTS anuncis(
                id INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(50) NULL UNIQUE,
                descripcio VARCHAR(255) NULL,
                preu INT NULL,
                categoria VARCHAR(255) NULL,
                imgProducte VARCHAR(255) NULL,
                id_usuari INT NULL,
                data_registre VARCHAR(50) NULL,
                FOREIGN KEY (id_usuari) REFERENCES usuaris(id)
            )";
            
            //confirmem que s'ha creat correctament
            if($conexio -> query($novaTaulaAnuncis))
            {
                echo "Taula creada anuncis correctament<br>";
            }
            else
            {
                echo "Error al crear la taula: ". $conexio->error."<br>";
            }
            
            //data actual
            $actualDate = date("d-m-y");

            //insertem a la taula els registres dels anuncis
            $newQueryContingut = "
            INSERT INTO anuncis (nom,descripcio,preu,categoria,imgProducte,id_usuari,data_registre)
            VALUES
            ('Iphone 13','Teléfono móvil Apple iPhone 13 Pro, 256 GB color grafito en perfecto estado con caja y accesorios originales.',850,'Electrónica','https://r-on.iservices.com/img/p/1/1/6/9/6/11696.jpg',2,'$actualDate'),
            ('Sofa','Sofá de piel color beige cómodo y elegante con asientos reclinables. Medidas: 2,10 m de largo.',450,'Hogar','https://tuttoconfortmurcia.com/wp-content/uploads/2016/12/toscana1.jpg',2,'$actualDate'),
            ('Bicicleta','Bicicleta de montaña Trek Marlin 7 talla M en excelente estado con frenos de disco hidráulicos y suspensión delantera.',600,'Deporte','https://la-madrilena.es/wp-content/uploads/2021/10/Monty_KX8_26-scaled.jpg',2,'$actualDate'),
            ('Cien años de soledad','Edición de coleccionista de la obra de Gabriel García Márquez tapa dura y en excelente estado.',55,'Libros','https://cdn.grupoelcorteingles.es/SGFM/dctm/MEDIA03/202401/31/00106524066989____2__1200x1200.jpg',2,'$actualDate'),
            ('Nintendo Switch','Consola Nintendo Switch con dos Joy-Con, cargador y base.',350,'Videojuegos','https://m.media-amazon.com/images/I/81IQp9uUdRL.jpg',2,'$actualDate')";

            //confirmem que els registres s'han creat correctament
            if($conexio -> query($newQueryContingut))
            {
                echo "Anuncis insertats correctament.<br>";
            }
            else
            {
                echo "Error al crear els anuncis:  ". $conexio->error."<br>";
            }

        ?></li>
    </ol>
    <?php
        //Quan creem la BBDD també creem l'arxiu anuncis.json amb la mateixa informació
        //Busquem la taula a BBDD y la guardem
        $anuncisBBDD = $conexio->query("SELECT * FROM anuncis");
        $anuncisJSON = [];
        while ($row = $anuncisBBDD->fetch(PDO::FETCH_ASSOC)) {
            $anuncisJSON[] = $row;
        }

        //recoguim el fitxer anuncis.json on volem guardar l'informació y li copiem l'informació estreta de BBDD
        $file = '../api/anuncis.json';
        // Leer el archivo JSON
        $anuncis = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

        $anuncis[]=$anuncisJSON;
        file_put_contents($file,json_encode($anuncis,JSON_PRETTY_PRINT));
    ?>
    <?php
        //tanquem la sessió
        $conexio = null;
    ?>
</body>
</html>