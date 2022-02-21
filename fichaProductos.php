<?php
    include ("conexion.php");

    session_start();

    if (time() - $_SESSION['lastRequestTime'] > 300) {
        header("Location: login.php");
    }

    $_SESSION['lastRequestTime'] = time();
    $bd = SingletonCon::getInstance(SingletonCon::UD4);
    $conexion = $bd->conectar();

    function eliminarProducto($idProducto) {
        global $bd;
        $bd->eliminar("delete from producto where idProducto = $idProducto");
    }

    function crearProducto($nombre, $tipo, $unidad, $descripcion, $pvp, $descuento) {
        global $bd;
        $bd->insertar("insert into producto (ProductoNombre, idTipoProducto, Unidad, Descripcion, pvpUnidad, Descuento)" .
                    " values ('$nombre', $tipo, '$unidad', '$descripcion', $pvp, $descuento)");
    }

    function getNuevaIdProducto() {
        global $bd;

        $resNuevaId = $bd->seleccionar("select max(idProducto) from producto");
        $nuevaId = $resNuevaId->fetch_assoc();

        return $nuevaId['max(idProducto)'] + 1;
    }

    if (isset($_POST['eliminar'])) {
        eliminarProducto($_POST['eliminar']);
    } else if (isset($_POST['nombreNuevo'])) {
        crearProducto(
            $_POST['nombreNuevo'],
            $_POST['tipoNuevo'],
            $_POST['unidadNuevo'],
            $_POST['descripcionNuevo'],
            $_POST['pvpNuevo'],
            $_POST['descuentoNuevo']);
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
                    <th>Tipo</th>
                    <th>Unidad</th>
                    <th>Descripci&oacute;n</th>
                    <th>PVP</th>
                    <th>Descuento</th>
                    <th>Eliminar</th>
                </tr>
                <?php
                    $resProd = $bd->seleccionar("select * from producto");
                    while ($prod = $resProd->fetch_assoc()) {
                ?>
                    <form method="POST" action=<?php echo $_SERVER['PHP_SELF'];?>>
                        <tr>
                            <td><?php echo $prod['ProductoNombre'];?></td>
                            <td><?php echo $prod['idTipoProducto'];?></td>
                            <td><?php echo $prod['Unidad'];?></td>
                            <td><?php echo $prod['Descripcion'];?></td>
                            <td><?php echo $prod['pvpUnidad'];?></td>
                            <td><?php echo $prod['Descuento'];?></td>
                            <td><input type="submit" value="Eliminar"></td>
                            <input type="hidden" name="eliminar" value=<?php echo $prod['idProducto']?>>
                        </tr>
                    </form>
                <?php }?>
            </table>
        </div>

        <div class="content">
            <form method="POST" action=<?php echo $_SERVER['PHP_SELF'];?>>
                <label for="nombreNuevo">Nombre:</label>
                <input type="text" id="nombreNuevo" name="nombreNuevo"><br>
                
                <label for="tipoNuevo">Tipo:</label>
                <select id="tipoNuevo" name="tipoNuevo">
                    <?php
                        $resTipoProd = $bd->seleccionar("select * from tipo_producto");
                        while ($tipoProd = $resTipoProd->fetch_assoc()) {
                    ?>
                        <option value="<?php echo $tipoProd['idTipo_producto']?>"><?php echo $tipoProd['DescTipoProd']?></option>
                    <?php }?>
                </select><br>

                <label for="unidadNuevo">Unidad:</label>
                <input type="text" id="unidadNuevo" name="unidadNuevo" value="ud"><br>

                <label for="descripcionNuevo">Descripci&oacute;n:</label>
                <input type="text" id="descripcionNuevo" name="descripcionNuevo"><br>

                <label for="pvpNuevo">PVP:</label>
                <input type="text" id="pvpNuevo" name="pvpNuevo" value="0"><br>

                <label for="descuentoNuevo">Descuento:</label>
                <input type="text" id="descuentoNuevo" name="descuentoNuevo" value="0"><br>

                <input type="submit">
            </form>
        </div>
    </body>
</html>
