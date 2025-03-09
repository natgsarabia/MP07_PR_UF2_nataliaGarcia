<!-- Iniciem la sessió -->
<?php session_start();  ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>
    <link rel=stylesheet href="../public/styleLogin.css">
</head>
<body class="bodyLogin">
    <div class="login">
        <h1>REGISTRAT</h1>
        <form class="login-form" method="POST" action="../controllers/loginUser.php">
            <p>Usuari: <input class="login-input" type="text" name="username" placeholder="Nom usuari" required></p>
            <p>Contrasenya: <input class="login-input" type="password" name="password"placeholder="Contrasenya"></p>
            
            <?php
                //revisem els errors de contrasenya actuals  
                $ActualError = isset($_SESSION['errors']) ? $_SESSION['errors']: 0;   

                //si son tres o més bloquejem el compte y no pot probar de nou, deshablitem el botó
                if($ActualError>=3){
                    echo "<button class='btn btn-primary btn-block btn-large' type='sumbit' name='register' disabled>LOGIN</button>";
                    echo "<h2>Ha esgotat les proves de contrasenya. Compte bloquejat temporalment.</h2>";
                    $_SESSION['errors']=0;
                }
                else //en cas contrari el botó está actiu
                {
                    echo "<button class='btn btn-primary btn-block btn-large' type='sumbit' name='register'>LOGIN</button>";       
                }
                
                ?>
        </form>
    </div>
</body>
</html>