<?php

    /**
     * Es innecesario pero queria probar a hacer un patrón Singleton.
     */
    class SingletonCon {
        public const UD4 = "BD_UD4";
        public const SERVICIO = "BD_SERVICIO";
        private static $conexiones = [];

        public static function getInstance($bd) {
            switch ($bd) {
                case self::UD4:
                    if (!isset($conexiones[self::UD4])) {
                        $conexiones[self::UD4] = new BDUD4();
                        $conexiones[self::UD4]->conectar();
                    }
                    
                    return $conexiones[self::UD4];
                    
                case self::SERVICIO:
                    if (!isset($conexiones[self::SERVICIO])) {
                        $conexiones[self::SERVICIO] = new BDServicio();
                        $conexiones[self::SERVICIO]->conectar();
                    }
                    
                    return $conexiones[self::SERVICIO];

                default:
                    throw new Exception("La Base de Datos '" . $bd . "' no existe.");
            }
        }
    }

    interface BaseDatos {
        public function conectar();
        public function query($query);
    }

    class BDUD4 implements BaseDatos {
        private $conexion;
        private $user ;
        private $host;
        private $pass ;
        private $db;
        
        public function __construct() {
            $this->user = "root";
            $this->host = "localhost";
            $this->pass = "";
            $this->db = "ud04";
        }

        /* Establece la conexión la BD.*/
        public function conectar() {
            $this->conexion = new mysqli($this->host,$this->user,$this->pass,$this->db);

            if ($this->conexion->connect_errno) {
                printf("Connect failed: %s\n", $mysqli->connect_error);
                exit();
            } else {
                return $this->conexion;
            }       
        }

        public function query($query) {
            $resultado=$this->conexion->query($query);

            if (!$resultado) {
                echo 'Hubo un error en la conexión con la base de datos.';
            }

            return $resultado;
        }

        public function seleccionar($query) {
            $resultado=$this->conexion->query($query);

            if (!$resultado) {
                echo 'Hubo un error en la conexión con la base de datos.';
                exit;
            }

            return $resultado;
        }

        public function insertar($query) {
            $resultado=$this->conexion->query($query);

            if (!$resultado) {
                echo "Los datos no pudieron ser enviados a la base de datos. <br>";
            } 
        }

        public function eliminar($query) {
            $resultado=$this->conexion->query($query);

            if (!$resultado) {
                echo "Los datos no pudieron ser enviados a la base de datos. <br>";
            } 
        }

        public function update($query) {
            $resultado=$this->conexion->query($query);

            if (!$resultado) {
                echo "Los datos no pudieron ser enviados a la base de datos. <br>";
            } 
        }

        function findOrCreateCesta() {
            global $bd;
    
            if (!$_SESSION['usuario']['Administrador']) {
                $resCesta = $bd->seleccionar("select * from cesta where cesta.idusuario = " . $_SESSION['usuario']['idusuario'] . " and cesta.comprado = 'N'");
                $cesta = $resCesta->fetch_assoc();
        
                if (!isset($cesta['idcesta'])) {
                    $bd->insertar("insert into cesta (idusuario, comprado) values (" . $_SESSION['usuario']['idusuario'] . ", 'N')");
                    $resCesta = $bd->seleccionar("select * from cesta where cesta.idusuario = " . $_SESSION['usuario']['idusuario'] . " and cesta.comprado = 'N'");
                    $cesta = $resCesta->fetch_assoc();
                }
            }
    
            return $cesta;
        }
    }

    class BDServicio implements BaseDatos {
        private $conexion;
        private $user ;
        private $host;
        private $pass ;
        private $db;
        
        public function __construct() {
            $this->user = "root";
            $this->host = "localhost";
            $this->pass = "";
            $this->db = "servicio";
        }

        /* Establece la conexión la BD.*/
        public function conectar() {
            $this->conexion = new mysqli($this->host,$this->user,$this->pass,$this->db);

            if ($this->conexion->connect_errno) {
                printf("Connect failed: %s\n", $mysqli->connect_error);
                exit();
            } else {
                return $this->conexion;
            }       
        }

        public function query($query) {
            $resultado=$this->conexion->query($query);

            if (!$resultado) {
                echo 'Hubo un error en la conexión con la base de datos.';
            }

            return $resultado;
        }
    }
?>
