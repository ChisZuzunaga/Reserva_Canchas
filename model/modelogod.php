<?php

require_once(__DIR__ . '/../db/Database.php');
$database = new Database();

class Clientes_model {
    private $conexion;

    public function __construct($database) {
        // Usar la conexión proporcionada por el objeto Database
        $this->conexion = $database->getConexion();

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

    public function contarClientes($emailr) {
        $query = "SELECT count(*) FROM cliente WHERE Email = :email";
        $statement = $this->conexion->prepare($query);
        $statement->bindParam(':email', $emailr);
        
        if ($statement->execute()) {
            return $statement->fetchColumn(); // Devuelve el conteo de registros
        }
        
        return false; // Retorna false si la ejecución falla
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
    public function reservarCancha($cancha_id, $fecha, $hora_inicio, $duracion, $precio) {
        if ($this->verificarDisponibilidad($cancha_id, $fecha, $hora_inicio, $duracion)) {
            $hora_inicio_timestamp = strtotime($hora_inicio);
            $hora_fin_timestamp = $hora_inicio_timestamp + ($duracion * 60); // Duración en segundos
            $hora_fin = date('H:i:s', $hora_fin_timestamp);

            $email = $_SESSION['session_email'];
            $nombre = $_SESSION['session_nombre'];
            
            $query = "INSERT INTO reserva (Fecha, Hora_Inicio, Hora_Fin, Duracion, ID_Cancha, Email, Estado, Precio) 
                      VALUES (:fecha, :hora_inicio, :hora_fin, :duracion, :cancha_id, :email, 'reservado', :precio)";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(':email', $email);
            $statement->bindParam(':cancha_id', $cancha_id);
            $statement->bindParam(':fecha', $fecha);
            $statement->bindParam(':hora_inicio', $hora_inicio);
            $statement->bindParam(':hora_fin', $hora_fin);
            $statement->bindParam(':duracion', $duracion);
            $statement->bindParam(':precio', $precio);

            return $statement->execute();
        } else {
            return false;
        }
    }

    // Obtener reservas por fecha y cancha
    public function getReservasPorFecha($cancha_id, $fecha) {
        $query = "SELECT reserva.ID_Reserva, reserva.Fecha, cliente.Nombre, cliente.Numero, reserva.Estado, reserva.Duracion, reserva.Precio, Hora_Inicio, Hora_Fin FROM reserva INNER JOIN cliente ON cliente.Email = reserva.Email WHERE ID_Cancha = :cancha_id AND Fecha = :fecha AND (Estado = 'reservado' or Estado = 'confirmada')";
        $statement = $this->conexion->prepare($query);
        $statement->bindParam(':cancha_id', $cancha_id);
        $statement->bindParam(':fecha', $fecha);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cancelarReserva($id_reserva) {
        $query = "UPDATE reserva SET Estado = 'cancelada' WHERE ID_Reserva = :id_reserva";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id_reserva', $id_reserva);
        return $stmt->execute();
    }
    
    public function cancelarReservaUSU($id_reserva) {
        $query = "UPDATE reserva SET Estado = 'cancelada' WHERE ID_Reserva = :id_reserva";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id_reserva', $id_reserva);
        return $stmt->execute();
    }

    public function confirmarReserva($id_reserva) {
        $query = "UPDATE reserva SET Estado = 'confirmada' WHERE ID_Reserva = :id_reserva";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id_reserva', $id_reserva);
        return $stmt->execute();
    }
    
    public function getReservasCanceladas() {
        $query = "SELECT reserva.ID_Reserva as ID_Reserva, reserva.Fecha as Fecha, reserva.Hora_Inicio as Hora_Inicio, reserva.Hora_Fin as Hora_Fin,
                  reserva.Duracion as Duracion, reserva.ID_Cancha as ID_Cancha, cliente.Email as Email, reserva.Estado as Estado, reserva.Precio as Precio,
                  cliente.Nombre as Nombre, cliente.Numero as Numero
                  FROM reserva INNER JOIN cliente ON cliente.Email = reserva.Email
                  WHERE Estado = 'cancelada' ORDER BY Fecha";
        $statement = $this->conexion->prepare($query);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReservasUsuario($emailr) {
        $query = "SELECT ID_Reserva, Fecha, Hora_Inicio, Hora_Fin, Duracion, ID_Cancha, Email, Estado, Precio
                  FROM reserva 
                  WHERE Email = :email";
        $statement = $this->conexion->prepare($query);
        $statement->bindParam(':email', $emailr, PDO::PARAM_STR); // Especifica el tipo de parámetro
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerInformePorDia($fecha) {
        $query = "SELECT * FROM Reserva WHERE Fecha = :fecha";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerInformePorSemana($fechaInicio, $fechaFin) {
        $query = "SELECT * FROM Reserva WHERE Fecha BETWEEN :inicio AND :fin";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':inicio', $fechaInicio);
        $stmt->bindParam(':fin', $fechaFin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerInformePorMes($mes, $año) {
        $query = "SELECT * FROM Reserva WHERE MONTH(Fecha) = :mes AND YEAR(Fecha) = :anio";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':mes', $mes);
        $stmt->bindParam(':anio', $año);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerInformePorRango($fechaInicio, $fechaFin) {
        $query = "SELECT * FROM Reserva WHERE Fecha BETWEEN :inicio AND :fin";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':inicio', $fechaInicio);
        $stmt->bindParam(':fin', $fechaFin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTotalClientes() {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM cliente");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    //consulta mala
    public function obtenerNuevosClientes() {
        $stmt = $this->conexion->prepare("SELECT COUNT(DISTINCT cliente.Email) AS cantidad_clientes FROM cliente JOIN reserva ON cliente.Email = reserva.Email GROUP BY cliente.Email HAVING COUNT(reserva.Email) = 1;");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function obtenerClientesAntiguos() {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM (SELECT reserva.Email FROM reserva GROUP BY reserva.Email HAVING COUNT(reserva.Email) > 1) AS usuarios_con_varias_reservas;");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function obtenerClientesSinReservas() {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM cliente c LEFT JOIN reserva r ON c.Email = r.Email WHERE r.Email IS NULL;");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function obtenerTotalReservas() {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM reserva");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function obtenerReservasConfirmadas() {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM reserva WHERE estado = 'confirmada'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function obtenerReservasPendientes() {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM reserva WHERE estado = 'reservado'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function obtenerReservasCanceladas() {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM reserva WHERE estado = 'cancelada'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function obtenerUsoCancha($id_cancha) {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM reserva WHERE id_cancha = ?");
        $stmt->execute([$id_cancha]);
        return $stmt->fetchColumn();
    }
    
    public function obtenerHorariosFrecuentes() {
        $stmt = $this->conexion->prepare("SELECT hora_inicio, COUNT(*) as cantidad FROM reserva GROUP BY hora_inicio ORDER BY cantidad DESC");
        $stmt->execute();
        $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular porcentaje de cada horario
        $total_reservas = array_sum(array_column($horarios, 'cantidad'));
        foreach ($horarios as &$horario) {
            $horario['porcentaje'] = round(($horario['cantidad'] / $total_reservas) * 100, 2); // Agregar porcentaje
        }
        
        return $horarios;
    }
    
    public function obtenerDiasFrecuentes() {
        $stmt = $this->conexion->prepare("SELECT DAYNAME(fecha) as dia, COUNT(*) as cantidad FROM reserva GROUP BY dia ORDER BY cantidad DESC");
        $stmt->execute();
        $dias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Traducir los días de inglés a español
        $dias_traducidos = [
            "Monday" => "Lunes",
            "Tuesday" => "Martes",
            "Wednesday" => "Miércoles",
            "Thursday" => "Jueves",
            "Friday" => "Viernes",
            "Saturday" => "Sábado",
            "Sunday" => "Domingo"
        ];
    
        foreach ($dias as &$dia) {
            // Traducir cada día
            $dia['dia'] = $dias_traducidos[$dia['dia']] ?? $dia['dia']; 
        }
        
        // Calcular porcentaje de cada día
        $total_reservas = array_sum(array_column($dias, 'cantidad'));
        foreach ($dias as &$dia) {
            $dia['porcentaje'] = round(($dia['cantidad'] / $total_reservas) * 100, 2); // Agregar porcentaje
        }
    
        return $dias;
    }
    
}

?>