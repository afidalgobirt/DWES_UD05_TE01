<?php
    include ("conexion.php");

    session_start();

    if (time() - $_SESSION['lastRequestTime'] > 300) {
        header("Location: login.php");
    }

    $_SESSION['lastRequestTime'] = time();
    $bd = SingletonCon::getInstance(SingletonCon::UD4);
    $conexion = $bd->conectar();
    $idTipo = (isset($_GET['idTipo']) ? $_GET['idTipo'] : $_POST['idTipo']);
    $idUsuario = $_SESSION['usuario']['idusuario'];

    // Incrementar el número de visitas al tipo de producto actual.
    if (isset($_COOKIE["visitedProductType_" . $idUsuario . "_" . $idTipo])) {
        $_COOKIE["visitedProductType_" . $idUsuario . "_" . $idTipo]++;
    } else {
        $_COOKIE["visitedProductType_" . $idUsuario . "_" . $idTipo] = 1;
    }
    
    // Actualizar cookie de numero de visitas al tipo de producto.
    setcookie(
        "visitedProductType_" . $idUsuario . "_" . $idTipo,
        $_COOKIE["visitedProductType_" . $idUsuario . "_" . $idTipo],
        time() + 60 * 60 * 24 * 30 // Caduca en 30 dias
    );

    function eliminarLinea($idLinea) {
        global $bd;
        $bd->eliminar("delete from cesta_lineas where cesta_lineas.idcesta_lineas = $idLinea");
    }

    function aniadirLinea($idProducto, $cantidad) {
        global $bd;

        $cesta = $bd->findOrCreateCesta();

        $bd->insertar("insert into cesta_lineas (idcesta, idproducto, cantidad) values (" . $cesta['idcesta'] . ", $idProducto, $cantidad)");
    }

    function eliminarLineaPorProducto($idProducto) {
        global $bd;
        $bd->eliminar("delete from cesta_lineas where cesta_lineas.idproducto = $idProducto");
    }

    if (isset($_POST['eliminarLineaId'])) {
        eliminarLinea($_POST['eliminarLineaId']);

    } else if (isset($_POST['insertarProducto'])) {
        eliminarLineaPorProducto($_POST['insertarProducto']);
        aniadirLinea($_POST['insertarProducto'], $_POST['cantidad']);

    } else if (isset($_POST['eliminarProducto'])) {
        eliminarLineaPorProducto($_POST['eliminarProducto']);
    }
?>

<html>
    <head>
        <link rel="stylesheet" href="css/style.css"/>
    </head>

    <body>
        <a href="login.php">logout</a>
        <div class="header">
            <?php
                $resTipoProd = $bd->seleccionar("select * from tipo_producto where tipo_producto.idTipo_producto = $idTipo");
                $tipoProd = $resTipoProd->fetch_assoc();

                echo "<h1>PRODUCTOS - " . strToUpper($tipoProd['DescTipoProd']) . "</h1>";
            ?>
        </div>

        <div class="nav">
            <a class="botonNav" href="principal.php">HOME</a>
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
                    <th>Cantidad</th>
                    <th>Añadir</th>
                    <th>Eliminar</th>
                </tr>
                <?php
                    $resProd = $bd->seleccionar("select * from producto where producto.idTipoProducto = $idTipo");
                    while ($prod = $resProd->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?php echo $prod['ProductoNombre'];?></td>
                        <td><?php echo $prod['idTipoProducto'];?></td>
                        <td><?php echo $prod['Unidad'];?></td>
                        <td><?php echo $prod['Descripcion'];?></td>
                        <td><?php echo $prod['pvpUnidad'];?></td>
                        <td><?php echo $prod['Descuento'];?></td>
                        <form method="POST" action=<?php echo $_SERVER['PHP_SELF'];?>>
                            <td><input type="text" name="cantidad"></td>
                            <td><input type="submit" value="Añadir"></td>
                            <input type="hidden" name="insertarProducto" value="<?php echo $prod['idProducto'];?>">
                            <input type="hidden" name="idTipo" value=<?php echo $idTipo;?>>
                        </form>
                        <form method="POST" action=<?php echo $_SERVER['PHP_SELF'];?>>
                            <td><input type="submit" value="Eliminar"></td>
                            <input type="hidden" name="eliminarProducto" value=<?php echo $prod['idProducto'];?>>
                            <input type="hidden" name="idTipo" value=<?php echo $idTipo;?>>
                        </form>
                    </tr>
                <?php }?>
            </table>
        </div>

        <div class="content">
            <h1>CESTA</h1><br>
            <table class="tablaProductos">
                <tr>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Descuento</th>
                    <th>Cantidad</th>
                    <th>Importe</th>
                    <th>Eliminar</th>
                </tr>
                
                <?php 
                    $cesta = $bd->findOrCreateCesta();
                    $resCestaLineas = $bd->seleccionar(
                        "select * from cesta_lineas " .
                        "join producto " .
                            "on producto.idProducto = cesta_lineas.idproducto " .
                            "where cesta_lineas.idcesta = " . $cesta['idcesta'] . " && " .
                                  "producto.idTipoProducto = $idTipo");
                    
                    while ($cestaLineas = $resCestaLineas->fetch_assoc()) {
                        $resProducto = $bd->seleccionar("select * from producto where producto.idProducto = " . $cestaLineas['idproducto']);
                        $producto = $resProducto->fetch_assoc();
                ?>
                    <tr>
                        <form method="POST" action=<?php echo $_SERVER['PHP_SELF'];?>>
                            <td><?php echo $producto['ProductoNombre'];?></td>
                            <td><?php echo $producto['pvpUnidad'];?></td>
                            <td><?php echo $producto['Descuento'];?></td>
                            <td><?php echo $cestaLineas['cantidad'];?></td>
                            <td><?php echo ($producto['pvpUnidad'] - $producto['Descuento']) * $cestaLineas['cantidad'];?></td>
                            <input type="hidden" name="eliminarLineaId" value=<?php echo $cestaLineas['idcesta_lineas'];?>>
                            <td><input type="submit" value="Eliminar"></td>
                            <input type="hidden" name="idTipo" value=<?php echo $idTipo;?>>
                        </form>
                    </tr>
                <?php }?>
            </table>
        </div>
    </body>
</html>
