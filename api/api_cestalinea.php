<?php
    header("Content-Type: application/json");
    include("cestalinea.php");

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (isset($_GET['id'])) {
                echo CestaLinea::getProductsById($_GET['id']);
            }

            break;
    }
?>