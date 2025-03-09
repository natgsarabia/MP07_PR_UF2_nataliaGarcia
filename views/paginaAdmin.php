<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pagina Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel=stylesheet href="../public/styleAdmin.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a href="../views/paginaAdmin.php" class="navbar-brand">GESTIÓ<b>Informació</b></a>  
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span> 
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="nav-item dropdown">
                <!-- En cas de que l'usuari registrat no sigui administrado no podrá
                 visualitzar els boton de regsitres, aixó mateix ho repliquem a tots els html.twig -->
                <?php
                    session_start();
                    if(isset($_SESSION['rol'])=='Administrador')
                    {
                        echo '<a data-toggle="dropdown" class="nav-item nav-link dropdown-toggle">Afeguir registres</a>';
                        echo '<div class="dropdown-menu">';				
                        echo '<a href="../views/paginaAfegirUsuari.php" class="dropdown-item">Crear nou usuari</a>';
                        echo '<a href="../views/paginaAfegirAnunci.php" class="dropdown-item">Crear nou anunci</a>';
                        echo '</div>';
                    }
                    
                ?>
            </div>   
            <a href="../views/paginaAnuncis.php" class="nav-item nav-link active">Anuncis</a>
            <div class="navbar-nav action-buttons ml-auto">
                <a href="../controllers/logoutUser.php" class="btn btn-primary">Tancar sessió</a>
            </div>
        </div>
    </nav>
    <main>
        <h1>BENVINGUT A LA PÀGINA DE ADMIN</h1>
        <div class="adminContainer">
            <p>Que vol fer: </p>
            <div class="adminButtons">
                <button class="custom-btn btn-5" id="newUser"  name='newUser'>AFEGIR NOU USUARI</button>
                <button class="custom-btn btn-5" id="newAnunci" name='newAnunci'>AFEGIR NOU ANUNCI</button>
            </div>
        </div>
    </main>
    <div class="footer">
       Proyecte Final UF2 MP07 <strong>Natalia García</strong> 
    </div>

    <script  src="../public/script.js" ></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>