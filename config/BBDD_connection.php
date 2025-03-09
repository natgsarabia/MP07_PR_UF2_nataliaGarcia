<?php
    //importem l'informació de l'arxiu de configuració
    require_once __DIR__ . '/../vendor/autoload.php';
    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    //conectem amb la BBDD
    try {
        $conexio = new PDO(
            "mysql:host=" . $_ENV['host'],
            $_ENV['username'],
            $_ENV['password']
        );

        $conexio->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    } 
    catch (PDOException $e) 
    {
        echo "Error de connexió a la base de dades: " . $e->getMessage();
    }

?>