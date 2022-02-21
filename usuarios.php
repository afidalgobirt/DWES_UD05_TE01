<?php
    include ("conexion.php");

    session_start();

    if (time() - $_SESSION['lastRequestTime'] > 300) {
        header("Location: login.php");
    }

    $_SESSION['lastRequestTime'] = time();
    $bd = SingletonCon::getInstance(SingletonCon::UD4);
    $conexion = $bd->conectar();

    if (isset($_POST['eliminar'])) {
        eliminarUsuario($_POST['eliminar']);
    } else if (isset($_POST['nombreNuevo'])) {
        crearUsuario(
            $_POST['nombreNuevo'],
            $_POST['apellidosNuevo'],
            $_POST['usernameNuevo'],
            $_POST['passNuevo'],
            isset($_POST['adminNuevo']));
    }

    function eliminarUsuario($idUsuario) {
        global $bd;
        $bd->eliminar("delete from usuario where idusuario = $idUsuario");
    }

    function crearUsuario($nombre, $apellidos, $username, $passwd, $admin) {
        global $bd;

        $admin = ($admin) ? "1" : "0";
        $bd->insertar(
            "insert into usuario (Nombre, Apellidos, UserName, Pass, Administrador) values " .
            "('$nombre', '$apellidos', '$username', '" . password_hash($passwd, PASSWORD_DEFAULT) . "', $admin)");
    }
?>

<html>
    <head>
        <link rel="stylesheet" href="css/style.css"/>
    </head>

    <body>
        <a href="login.php">logout</a>
        <div class="header">
            <h1>TIENDA BIRT</h1>
        </div>

        <div class="nav">
            <a class="botonNav" href="principal.php">HOME</a>
            <a class="botonNav" href="fichaProductos.php">Producto</a>
            <a class="botonNav" href="usuarios.php">Usuario</a>
        </div>

        <div class="content">
            <table class="tablaProductos">
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Nombre de usuario</th>
                    <th>Contraseña</th>
                    <th>Administrador</th>
                    <th>Eliminar</th>
                </tr>
                <?php
                    $resUser = $bd->seleccionar("select * from usuario");
                    while ($user = $resUser->fetch_assoc()) {
                ?>
                    <form method="POST" action=<?php echo $_SERVER['PHP_SELF'];?>>
                        <tr>
                            <td><?php echo $user['Nombre'];?></td>
                            <td><?php echo $user['Apellidos'];?></td>
                            <td><?php echo $user['UserName'];?></td>
                            <td><?php echo $user['Pass'];?></td>
                            <td><?php echo $user['Administrador'];?></td>
                            <td><input type="submit" value="Eliminar"></td>
                            <input type="hidden" name="eliminar" value=<?php echo $user['idusuario']?>>
                        </tr>
                    </form>
                <?php }?>
            </table>
        </div>

        <div class="content">
            <form method="POST" action=<?php echo $_SERVER['PHP_SELF'];?>>
                <label for="nombreNuevo">Nombre:</label>
                <input type="text" id="nombreNuevo" name="nombreNuevo"><br>
                
                <label for="apellidosNuevo">Apellidos:</label>
                <input type="text" id="apellidosNuevo" name="apellidosNuevo"><br>

                <label for="usernameNuevo">Nombre de usuario:</label>
                <input type="text" id="usernameNuevo" name="usernameNuevo"><br>

                <label for="passNuevo">Contraseña:</label>
                <input type="password" id="passNuevo" name="passNuevo"><br>

                <label for="adminNuevo">Administrador:</label>
                <input type="checkbox" id="adminNuevo" name="adminNuevo"><br>

                <input type="submit">
            </form>
        </div>
    </body>
</html>
