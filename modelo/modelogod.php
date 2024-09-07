<?php
class Clientes_model {
    private $conexion;

    public function __construct() {
        // Realiza la conexión a la base de datos
        $this->conexion = new PDO("mysql:host=localhost;dbname=megareservas", "root", "");
        
        // Iniciar la sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
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
    
public function verificarDisponibilidad($cancha_id, $fecha, $hora_inicio, $duracion) {
    // Define el horario de cierre de la cancha
    $horario_cierre = '23:30:00';
    
    // Convertir la hora de inicio a timestamp
    $hora_inicio_timestamp = strtotime($hora_inicio);
    
    // Calcular la hora de fin en función de la duración
    $hora_fin_timestamp = $hora_inicio_timestamp + ($duracion * 60); // Duración en segundos
    $hora_fin = date('H:i:s', $hora_fin_timestamp);
    
    // Verificar que la hora de fin no exceda el horario de cierre
    if ($hora_fin_timestamp > strtotime($horario_cierre)) {
        // Si la hora de fin es posterior al horario de cierre, la reserva no es válida
        return false;
    }
    
    // Consulta SQL para verificar conflictos
    $query = "SELECT * FROM reserva 
              WHERE ID_Cancha = :cancha_id 
              AND Fecha = :fecha 
              AND ((Hora_Inicio < :hora_fin AND Hora_Fin > :hora_inicio)) 
              AND Estado = 'reservado'";
    $statement = $this->conexion->prepare($query);
    $statement->bindParam(':cancha_id', $cancha_id);
    $statement->bindParam(':fecha', $fecha);
    $statement->bindParam(':hora_inicio', $hora_inicio);
    $statement->bindParam(':hora_fin', $hora_fin);
    $statement->execute();
    
    // Devuelve true si no hay conflictos (ninguna fila encontrada)
    return $statement->rowCount() === 0;
}


    // Realizar la reserva de la cancha
    public function reservarCancha($cancha_id, $fecha, $hora_inicio, $duracion) {
        if ($this->verificarDisponibilidad($cancha_id, $fecha, $hora_inicio, $duracion)) {
            $hora_inicio_timestamp = strtotime($hora_inicio);
            $hora_fin_timestamp = $hora_inicio_timestamp + ($duracion * 60); // Duración en segundos
            $hora_fin = date('H:i:s', $hora_fin_timestamp);

            $email = $_SESSION['session_email'];
            
            $query = "INSERT INTO reserva (Fecha, Hora_Inicio, Hora_Fin, Duracion, ID_Cancha, Email, Estado) 
                      VALUES (:fecha, :hora_inicio, :hora_fin, :duracion, :cancha_id, :email, 'reservado')";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(':email', $email);
            $statement->bindParam(':cancha_id', $cancha_id);
            $statement->bindParam(':fecha', $fecha);
            $statement->bindParam(':hora_inicio', $hora_inicio);
            $statement->bindParam(':hora_fin', $hora_fin);
            $statement->bindParam(':duracion', $duracion);

            return $statement->execute();
        } else {
            return false;
        }
    }

    // Obtener reservas por fecha y cancha
    public function getReservasPorFecha($cancha_id, $fecha) {
        $query = "SELECT Hora_Inicio, Hora_Fin FROM reserva WHERE ID_Cancha = :cancha_id AND Fecha = :fecha AND Estado = 'reservado'";
        $statement = $this->conexion->prepare($query);
        $statement->bindParam(':cancha_id', $cancha_id);
        $statement->bindParam(':fecha', $fecha);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>
