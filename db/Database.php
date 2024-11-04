// db/Database.php
<?php

class Database {
    private $conexion;

    public function __construct() {
        // Cargar los datos de configuración de la base de datos
        $config = include(__DIR__ . '/../config/config.php');

        try {
            // Crear la conexión usando los datos de configuración
            $this->conexion = new PDO(
                "mysql:host={$config['db_host']};dbname={$config['db_name']}", 
                $config['db_user'], 
                $config['db_pass']
            );
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error al conectar a la base de datos: " . $e->getMessage());
        }
    }

    // Método para obtener la conexión en otras partes del código
    public function getConexion() {
        return $this->conexion;
    }
}
?>
