<?php
require_once '../modelo/modelogod.php'; // Ajusta la ruta según tu estructura de archivos
session_start();

if (isset($_SESSION['session_email'])) {


} else {
    echo "No has iniciado sesión.";
    exit();
}

// Inicializar variables
$cancha_id = isset($_GET['cancha_id']) ? $_GET['cancha_id'] : '';
$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
$duracion = isset($_POST['duracion']) ? intval($_POST['duracion']) : 60; // Duración predeterminada

// Verificar si se ha enviado una solicitud POST con hora_inicio
$horarios_ocupados = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['hora_inicio'])) {
    if ($fecha && $cancha_id) {
        $model = new Clientes_Model();

        // Obtener reservas para la fecha seleccionada y la cancha seleccionada
        $reservas = $model->getReservasPorFecha($cancha_id, $fecha);

        // Procesar las reservas para visualizar horarios ocupados
        foreach ($reservas as $reserva) {
            $hora_inicio = strtotime($reserva['Hora_Inicio']);
            $hora_fin = strtotime($reserva['Hora_Fin']);
            while ($hora_inicio < $hora_fin) {
                $horarios_ocupados[date('H:i', $hora_inicio)] = true;
                $hora_inicio += 1800; // Avanzar en intervalos de 30 minutos
            }
        }
    }
}

// Configuración de horarios
$hora_inicio = "07:00"; // Hora de inicio del día
$hora_fin = "22:00";   // Hora de fin del día
$intervalo = 30; // Intervalo en minutos

// Obtener los próximos 10 días para mostrar como botones
$fechas_disponibles = [];
$fecha_actual = date('Y-m-d'); // Comienza desde la fecha actual

for ($i = 0; $i < 10; $i++) {
    $fecha_disponible = date('Y-m-d', strtotime("+{$i} days", strtotime($fecha_actual)));
    $fechas_disponibles[] = $fecha_disponible;
}

// Manejar la reserva si se envía una solicitud POST con hora_inicio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hora_inicio'])) {
    $hora_inicio_reserva = $_POST['hora_inicio'];
    $cancha_id_reserva = $_POST['cancha_id'];
    $fecha_reserva = $_POST['fecha'];
    $duracion_reserva = $_POST['duracion'];

    // Lógica para manejar la reserva (esto depende de cómo manejes las reservas en tu controlador)
    // Por ejemplo, podrías redirigir al usuario a otra página para confirmar la reserva
    header("Location: ../controlador/controlador.php?action=reservar&cancha_id={$cancha_id_reserva}&fecha={$fecha_reserva}&hora_inicio={$hora_inicio_reserva}&duracion={$duracion_reserva}");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disponibilidad de Horarios</title>
    <style>
        .fecha-boton {
            margin: 5px;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #f0f0f0;
            cursor: pointer;
        }
        .fecha-boton:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <a href="inicio.php">Volver a página principal</a>
    <h1>Disponibilidad de Horarios para la Cancha <?php echo htmlspecialchars($cancha_id); ?></h1>
    
    <form id="formulario" action="" method="POST">
        <!--<label for="fecha">Fecha:</label>
        <input type="date" name="fecha" id="fecha" value="<?php echo htmlspecialchars($fecha); ?>" required>!-->
        <br><br>

        <label for="duracion">Duración (minutos):</label>
        <select name="duracion" id="duracion" onchange="this.form.submit()">
            <option value="60" <?php echo $duracion == 60 ? 'selected' : ''; ?>>60 minutos</option>
            <option value="90" <?php echo $duracion == 90 ? 'selected' : ''; ?>>90 minutos</option>
            <option value="120" <?php echo $duracion == 120 ? 'selected' : ''; ?>>120 minutos</option>
        </select>
        <br><br>
    </form>

    <div>
        <h2>Selecciona una fecha:</h2>
        <?php foreach ($fechas_disponibles as $fecha_disponible): ?>
            <form action="" method="POST" style="display: inline;">
                <input type="hidden" name="cancha_id" value="<?php echo htmlspecialchars($cancha_id); ?>">
                <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($fecha_disponible); ?>">
                <input type="hidden" name="duracion" value="<?php echo htmlspecialchars($duracion); ?>">
                <button type="submit" class="fecha-boton"><?php echo date('d-m', strtotime($fecha_disponible)); ?></button>
            </form>
        <?php endforeach; ?>
    </div>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $fecha && $cancha_id): ?>
        <form action="../controlador/controlador.php?action=reservar" method="POST">
            <table border="1">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Generar la tabla de horarios
                    $hora_actual = strtotime($hora_inicio);
                    $hora_final = strtotime($hora_fin);
                    while ($hora_actual <= $hora_final) {
                        $hora_formato = date('H:i', $hora_actual);
                        $estado = isset($horarios_ocupados[$hora_formato]) ? 'Ocupado' : 'Libre';
                        $disabled = isset($horarios_ocupados[$hora_formato]) ? 'disabled' : '';
                        $is_blocked = false;

                        // Verificar si el bloque actual está ocupado en función de la duración seleccionada
                        if (!$disabled) {
                            for ($i = 0; $i < $duracion / 30; $i++) {
                                $check_time = date('H:i', strtotime("+{$i} * 30 minutes", $hora_actual));
                                if (isset($horarios_ocupados[$check_time])) {
                                    $is_blocked = true;
                                    break;
                                }
                            }
                            $disabled = $is_blocked ? 'disabled' : '';
                        }

                        echo "<tr>
                                <td>{$hora_formato}</td>
                                <td><button type='submit' name='hora_inicio' value='{$hora_formato}' {$disabled}>Reservar</button></td>
                              </tr>";
                        $hora_actual = strtotime("+{$intervalo} minutes", $hora_actual);
                    }
                    ?>
                </tbody>
            </table>
            <input type="hidden" name="cancha_id" value="<?php echo htmlspecialchars($cancha_id); ?>">
            <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>">
            <input type="hidden" name="duracion" value="<?php echo htmlspecialchars($duracion); ?>">
        </form>
    <?php endif; ?>
</body>
</html>
