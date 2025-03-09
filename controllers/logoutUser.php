<?php 
    //destruim la sessió actual
    session_destroy();
    
    //retornem a la página d'inici
    header("Location: ../public/index.php");
    exit();
?>
