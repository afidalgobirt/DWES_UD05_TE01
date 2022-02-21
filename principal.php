<?php
    include("conexion.php");

    session_start();

    if (time() - $_SESSION['lastRequestTime'] > 300) {
        header("Location: login.php");
    }

    $_SESSION['lastRequestTime'] = time();
    $bd = SingletonCon::getInstance(SingletonCon::UD4);
    $conexion = $bd->conectar();

    if (!$_SESSION['usuario']['Administrador']) {
        $cesta = $bd->findOrCreateCesta();
    }

    function eliminarLinea($idLinea) {
        global $bd;
        $bd->eliminar("delete from cesta_lineas where cesta_lineas.idcesta_lineas = $idLinea");
    }

    function comprarCesta($idcesta) {
        global $bd;
        global $cesta;

        $bd->update("update cesta set cesta.comprado = 'S' where cesta.idcesta = $idcesta");
        $cesta = $bd->findOrCreateCesta();
    }

    if (isset($_POST['eliminarLineaId'])) {
        eliminarLinea($_POST['eliminarLineaId']);
    }

    if (isset($_POST['comprarCesta'])) {
        comprarCesta($_POST['comprarCesta']);
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

        <?php if ($_SESSION['usuario']['Administrador']) {?>
            <div class="nav">
                <a class="botonNav" href="principal.php">HOME</a>
                <a class="botonNav" href="fichaProductos.php">Producto</a>
                <a class="botonNav" href="usuarios.php">Usuario</a>
            </div>
            <div class="content">
            <h1>PRODUCTOS BAJOS DE STOCK</h1><br>
                <table class="tablaProductos">
                    <tr>
                        <th>Producto</th>
                        <th>Stock</th>
                        <th>Entrega</th>
                    </tr>
                <?php
                    $url = "http://localhost/dwes_ud05_te01/servicio/servicio.php";
                    $uri = "http://localhost/dwes_ud05_te01/servicio";
                    $cliente = new SoapClient(null, array('location' => $url, 'uri' => $uri));
                    $productos = $cliente->getLowStockProducts();

                    for ($i = 0; $i < count($productos); $i++) {?>
                        <tr>
                            <td>
                                <?php
                                    $productoRes = $bd->query("select * from producto where idProducto = " . $productos[$i]['idProducto']);
                                    $producto = $productoRes->fetch_assoc();
                                    if (isset($producto['ProductoNombre'])) {
                                        echo $producto['ProductoNombre'];
                                    } else {
                                        echo "No existe";
                                    }
                                ?>
                            </td>
                            <td><?php echo $productos[$i]['stock'];?></td>
                            <td><?php echo date('d/m/Y', strtotime('+' . $productos[$i]['diasPedido'] . " days"));?></td>
                        </tr>
                <?php }?>
                </table>
            </div>
        <?php } else {?>
            <div class="content">
                <?php
                    $resTipoProd = $bd->seleccionar("select * from tipo_producto");
                    while ($tipoProd = $resTipoProd->fetch_assoc()) {
                ?>
                    <div class="itemTipoProducto">
                        <a href=<?php echo "producto.php?idTipo=" . $tipoProd['idTipo_producto'];?>><?php echo $tipoProd['DescTipoProd'];?></a>
                    </div>
                <?php }?>
            </div>

            <div class="content-vertical">
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
                        $resCestaLineas = $bd->seleccionar("select * from cesta_lineas where cesta_lineas.idcesta = ". $cesta['idcesta']);
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
                            </form>
                        </tr>
                    <?php }?>
                </table>
                <form method="POST" action=<?php echo $_SERVER['PHP_SELF'];?>>
                    <input type="hidden" name="comprarCesta" value=<?php echo $cesta['idcesta'];?>>
                    <input type="submit" value="Comprar" class="button"/>
                </form>
            </div>
            <div class="content-vertical">
                <h1>PRODUCTOS DESTACADOS</h1><br>
                <table class="tablaProductos">
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Unidad</th>
                        <th>Descripci&oacute;n</th>
                        <th>PVP</th>
                        <th>Descuento</th>
                    </tr>
                    <?php
                        $resTipoProd = $bd->seleccionar("select * from tipo_producto");
                        $maxTipoProd = 0;
                        $maxTipoProdCont = 0;
                        $idUsuario = $_SESSION['usuario']['idusuario'];

                        // Conseguir el tipo de producto mas visitado por el usuario.
                        while ($tipoProd = $resTipoProd->fetch_assoc()) {
                            if (isset($_COOKIE["visitedProductType_" . $idUsuario . "_" . $tipoProd['idTipo_producto']]) &&
                                $_COOKIE["visitedProductType_" . $idUsuario . "_" . $tipoProd['idTipo_producto']] > $maxTipoProdCont) {

                                $maxTipoProd = $tipoProd['idTipo_producto'];
                                $maxTipoProdCont = $_COOKIE["visitedProductType_" . $idUsuario . "_" . $tipoProd['idTipo_producto']];
                            } else if ($maxTipoProd == 0) {
                                $maxTipoProd = $tipoProd['idTipo_producto'];
                            }
                        }

                        $resProd = $bd->seleccionar("select * from producto where producto.idTipoProducto = $maxTipoProd");
                        while ($prod = $resProd->fetch_assoc()) {
                    ?>
                        <tr>
                            <td><?php echo $prod['ProductoNombre'];?></td>
                            <td><?php echo $prod['idTipoProducto'];?></td>
                            <td><?php echo $prod['Unidad'];?></td>
                            <td><?php echo $prod['Descripcion'];?></td>
                            <td><?php echo $prod['pvpUnidad'];?></td>
                            <td><?php echo $prod['Descuento'];?></td>
                        </tr>
                    <?php }?>
                </table>
                <a href=<?php echo "producto.php?idTipo=$maxTipoProd";?> class="button">Ver productos</a>
            </div>
        <?php }?>
    </body>
</html>
