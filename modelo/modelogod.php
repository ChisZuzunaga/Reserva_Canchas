<?php
class Clientes_model {
    private $conexion;

    public function __construct() {
        // Realiza la conexión a la base de datos
        $this->conexion = new PDO("mysql:host=localhost;dbname=megareservas", "root", "");
    }

    public function getReservas() {
        // Obtiene todos las RESERVAS de la base de datos
        $query = "SELECT * FROM reserva";
        
        $statement = $this->conexion->query($query);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getCanchas() {
        // Obtiene todos las CANCHAS de la base de datos
        $query = "SELECT * FROM cancha";
        
        $statement = $this->conexion->query($query);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getClientes() {
        // Obtiene todos los CLIENTES de la base de datos
        $query = "SELECT * FROM cliente";
        
        $statement = $this->conexion->query($query);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getHorarios() {
        // Obtiene todos los HORARIOS de la base de datos
        $query = "SELECT * FROM horario";
        
        $statement = $this->conexion->query($query);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function insertClientes($nombrer, $apellidor, $emailr, $claver, $numeror, $imagenr) {
        // Inserta un nuevo profesor en la base de datos
        $query = "INSERT INTO cliente (Nombre, Apellido, Email, Clave, Numero, Imagen) VALUES (:nombre, :apellido, :email, :clave, :numero, :imagen)";
        $statement = $this->conexion->prepare($query);
        $statement->bindParam(':nombre', $nombrer);
        $statement->bindParam(':apellido', $apellidor);
        $statement->bindParam(':email', $emailr);
        $statement->bindParam(':clave', $claver);
        $statement->bindParam(':numero', $numeror);
        $statement->bindParam(':imagen', $imagenr);
        $result = $statement->execute();
        return $result;
    }

    public function verifysClientes($emailr) {
        $query = "SELECT * FROM cliente WHERE Email = :email";
        $statement = $this->conexion->prepare($query);
        $statement->bindParam(':email', $emailr);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC); // Devuelve los datos del usuario, incluyendo el hash de la contraseña
    }
    


    public function updateProfesor($rut, $nombre, $apellidoPaterno, $apellidoMaterno, $correo, $tipoContrato) {
        // Actualiza los datos de un profesor en la base de datos
        $query = "UPDATE profesores SET nombre = :nombre, apellido_paterno = :apellidoPaterno, apellido_materno = :apellidoMaterno, correo = :correo, tipo_contrato = :tipoContrato WHERE rut = :rut";
        $statement = $this->conexion->prepare($query);
        $statement->bindParam(':rut', $rut);
        $statement->bindParam(':nombre', $nombre);
        $statement->bindParam(':apellidoPaterno', $apellidoPaterno);
        $statement->bindParam(':apellidoMaterno', $apellidoMaterno);
        $statement->bindParam(':correo', $correo);
        $statement->bindParam(':tipoContrato', $tipoContrato);
        $result = $statement->execute();
        return $result;
    }

   
}
?>
