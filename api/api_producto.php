<?php
    header("Content-Type: application/json");
    include("product.php");

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (isset($_GET['id'])) {
                echo Product::getProductById($_GET['id']);
            } else {
                echo Product::getProducts();
            }
            
            break;
        
        case 'POST':
            if (isset($_GET['ProductoNombre']) && isset($_GET['idTipoProducto']) &&
                isset($_GET['Unidad']) && isset($_GET['Description']) &&
                isset($_GET['pvpUnidad']) && isset($_GET['Descuento'])) {

                echo Product::postProduct(
                    $_GET['ProductoNombre'],
                    $_GET['idTipoProducto'],
                    $_GET['Unidad'],
                    $_GET['Description'],
                    $_GET['pvpUnidad'],
                    $_GET['Descuento']
                );
            }
            
            break;

        case 'PUT':
            if (isset($_GET['id']) && isset($_GET['precio'])) {
                Product::putProducto($_GET['id'], $_GET['precio']);
            }

            break;

        case 'DELETE':
            if (isset($_GET['id'])) {
                Product::deleteProduct($_GET['id']);
            }

            break;
    }
?>