<?php
    include("../conexion.php");

    class CestaLinea {
        public static function getProductsById($id) {
            $ret = [];

            $bd = SingletonCon::getInstance(SingletonCon::UD4);
            $productosRes = $bd->query(
                "select * from producto p " .
                "inner join cesta_lineas cl " .
                    "on cl.idproducto = p.idProducto " .
                    "where cl.idcesta = $id");
            
            while ($producto = $productosRes->fetch_assoc()) {
                array_push($ret, $producto);
            }

            return json_encode($ret);
        }
    }
?>