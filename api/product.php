<?php
    include("../conexion.php");

    class Product {
        public static function getProducts() {
            $ret = [];

            $bd = SingletonCon::getInstance(SingletonCon::UD4);
            $productsRes = $bd->query("select * from producto");

            while ($product = $productsRes->fetch_assoc()) {
                array_push($ret, $product);
            }

            return json_encode($ret);
        }

        public static function getProductById($id) {
            $bd = SingletonCon::getInstance(SingletonCon::UD4);
            $productRes = $bd->query("select * from producto where idProducto = " . intval($id));
            $product = $productRes->fetch_assoc();

            return json_encode($product);
        }

        public static function postProduct($productoNombre, $idTipoProducto, $unidad,
                                                $description, $pvpUnidad, $descuento) {
            $bd = SingletonCon::getInstance(SingletonCon::UD4);
            $bd->query(
                "insert into producto " .
                "(ProductoNombre, idTipoProducto, Unidad, Descripcion, pvpUnidad, Descuento) " .
                "values ('$productoNombre', $idTipoProducto, '$unidad', '$description', $pvpUnidad, $descuento)"
            );
        }

        public static function putProducto($id, $precio) {
            $bd = SingletonCon::getInstance(SingletonCon::UD4);
            $bd->query("update producto set pvpUnidad = $precio where idProducto = $id");
        }

        public static function deleteProduct($id) {
            $bd = SingletonCon::getInstance(SingletonCon::UD4);
            $bd->query("delete from producto where idProducto = $id");
        }
    }
?>