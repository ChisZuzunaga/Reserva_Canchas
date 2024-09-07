<?php
session_start(); // Asegúrate de iniciar la sesión

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['session_email'])) {
    echo "No has iniciado sesión.";
    exit();
}

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener la fecha y el ID de la cancha desde el formulario
    $fecha = $_POST['fecha'];
    $cancha_id = $_POST['cancha_id'];

    // Crear instancia del modelo
    require_once '../modelo/modelogod.php';
    $modelo = new Clientes_model();

    // Obtener reservas para la fecha y cancha seleccionadas
    $reservas = $modelo->getReservasPorFecha($cancha_id, $fecha);

    // Procesar las reservas para visualizar horarios ocupados
    $horarios_ocupados = [];
    foreach ($reservas as $reserva) {
        $hora_inicio = strtotime($reserva['Hora_Inicio']);
        $hora_fin = strtotime($reserva['Hora_Fin']);
        while ($hora_inicio < $hora_fin) {
            $horarios_ocupados[date('H:i', $hora_inicio)] = true;
            $hora_inicio += 1800; // Avanzar en intervalos de 30 minutos
        }
    }
} else {
    echo "No se ha seleccionado una fecha o cancha.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios Disponibles</title>
</head>
<body>
    <h1>Horarios Disponibles para el <?php echo htmlspecialchars($fecha); ?></h1>
    
    <table border="1">
        <tr>
            <th>Hora</th>
            <th>Estado</th>
        </tr>
        <?php
        $hora_actual = strtotime('07:00:00');
        $hora_final = strtotime('22:00:00');
        while ($hora_actual <= $hora_final) {
            $hora_formato = date('H:i', $hora_actual);
            $estado = isset($horarios_ocupados[$hora_formato]) ? 'Ocupado' : 'Disponible';
            echo "<tr><td>$hora_formato</td><td>$estado</td></tr>";
            $hora_actual += 1800; // Avanzar en intervalos de 30 minutos
        }
        ?>
    </table>

    <!-- Enlace para volver a la página de reservas -->
    <a href="reservar.php">Volver a Reservar</a>
</body>
</html>
