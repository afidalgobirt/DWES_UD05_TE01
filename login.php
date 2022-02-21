<?php 
    include ("conexion.php");

    if (isset($_SESSION['PHP_AUTH_USER'])) {
        session_abort();
    }

    $bd = SingletonCon::getInstance(SingletonCon::UD4);
    $conexion = $bd->conectar();

    if (isset($_POST['username'])) {
        login($_POST['username'], $_POST['password']);
    }

    function login($username, $password) {
        global $bd;

        $resUsuario = $bd->seleccionar("select * from usuario where usuario.UserName = '$username'");
        $usuario = $resUsuario->fetch_assoc();

        if (password_verify($password, $usuario['Pass'])) {
            session_start();

            $_SESSION['PHP_AUTH_USER'] = $usuario['UserName'];
            $_SESSION['PHP_AUTH_PW'] = $usuario['Pass'];
            $_SESSION['usuario'] = $usuario;
            $_SESSION['lastRequestTime'] = time();

            header("Location: principal.php");
        } else {
            echo "<h1>ERROR: Nombre de usuario o contrasenia incorrectos</h1>";
        }
    }
?>

<html>
    <head>
        <link rel="stylesheet" href="css/style.css"/>
    </head>
    <body>
        <section class="loginContainer">
            <form method="POST" action=<?php echo $_SERVER['PHP_SELF'];?>>
                <label for="username">Username</label>
                <input type="text" id="username" name="username"/><br>

                <label for="password">Password</label>
                <input type="password" id="password" name="password"/><br><br>

                <input type="submit"/>
            </form>
        </section>
    </body>
</html>
