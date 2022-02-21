<?php
    include_once("../conexion.php");

    const URI = "http://localhost/dwes_ud05_te01/servicio";
    const URL = "http://localhost/dwes_ud05_te01/servicio/servicio.php";

    function getLowStockProducts() {
        $ret = [];

        $bd = SingletonCon::getInstance(SingletonCon::SERVICIO);
        $productosRes = $bd->query("select * from proveedor where stock <= 4");
        
        while ($producto = $productosRes->fetch_assoc()) {
            array_push($ret, $producto);
        }

        return $ret;
    }
    
    $server = new SoapServer(null, array('uri' => URI));
    $server->addFunction('getLowStockProducts');
    $server->handle();
?>
