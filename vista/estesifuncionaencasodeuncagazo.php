<?php
require_once '../modelo/modelogod.php'; // Ajusta la ruta según tu estructura de archivos
session_start();

if (!isset($_SESSION['session_email'])) {
    echo "No has iniciado sesión.";
    exit();
}

// Inicializar variables
$cancha_id = isset($_GET['cancha_id']) ? $_GET['cancha_id'] : '';
$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d'); // Fecha predeterminada: hoy
$duracion = isset($_POST['duracion']) ? intval($_POST['duracion']) : 60; // Duración predeterminada

// Obtener las reservas para la fecha seleccionada
$horarios_ocupados = [];
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

// Configuración de horarios
$hora_inicio = "07:00"; // Hora de inicio del día
$hora_fin = "22:00";   // Hora de fin del día
$intervalo = 30; // Intervalo en minutos

// Obtener los días disponibles (hasta 10 días adicionales al día actual)
$fecha_actual = date('Y-m-d');
$offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0; // Para controlar las fechas a mostrar
$max_offset = 2; // Límite de días que se pueden mostrar (10 días adicionales)
$fechas_disponibles = [];

// Limitar el avance para que no supere los 10 días desde el día actual
for ($i = 0; $i < 10; $i++) {
    $fecha_disponible = date('Y-m-d', strtotime("+" . ($offset + $i) . " days", strtotime($fecha_actual)));
    $fechas_disponibles[] = $fecha_disponible;
}

// Manejar la reserva si se envía una solicitud POST con hora_inicio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hora_inicio'])) {
    $hora_inicio_reserva = $_POST['hora_inicio'];
    $cancha_id_reserva = $_POST['cancha_id'];
    $fecha_reserva = $_POST['fecha'];
    $duracion_reserva = $_POST['duracion'];

    // Redirigir al controlador para realizar la reserva
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

        .seleccionado {
            background-color: #4CAF50; /* Color verde */
            color: white; /* Color del texto */
        }

        .bloque-horas {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 10px;
        }

        h3 {
            margin: 0;
            text-align: center;
        }

        .hora-boton {
            margin: 5px;
            padding: 8px;
            border: 1px solid #ccc;
            background-color: #f0f0f0;
            cursor: pointer;
        }

        .hora-boton:hover {
            background-color: #ddd;
        }

        .ocupado {
            background-color: #e57373; /* Rojo para ocupado */
            cursor: not-allowed;
        }

        .reservar {
            background-color: #4CAF50; /* Verde para reservar */
            color: white; /* Color del texto */
        }
    </style>
</head>
<body>
    <a href="inicio.php">Volver a página principal</a>
    <h1>Disponibilidad de Horarios para la Cancha <?php echo htmlspecialchars($cancha_id); ?></h1>
    
    <form id="formulario" action="" method="POST">
        <label for="duracion">Duración (minutos):</label>
        <select name="duracion" id="duracion" onchange="this.form.submit()">
            <option value="60" <?php echo $duracion == 60 ? 'selected' : ''; ?>>60 minutos</option>
            <option value="90" <?php echo $duracion == 90 ? 'selected' : ''; ?>>90 minutos</option>
            <option value="120" <?php echo $duracion == 120 ? 'selected' : ''; ?>>120 minutos</option>
        </select>
        <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>"> <!-- Mantener la fecha seleccionada -->
        <input type="hidden" name="cancha_id" value="<?php echo htmlspecialchars($cancha_id); ?>"> <!-- También mantener el id de cancha -->
        <input type="hidden" name="offset" value="<?php echo $offset; ?>"> <!-- Offset para manejar las fechas -->
        <br><br>
    </form>
    
    <div>
        <h2>Selecciona una fecha:</h2>
        
        <!-- Botón de retroceder -->
        <form action="" method="POST" style="display: inline;">
            <input type="hidden" name="cancha_id" value="<?php echo htmlspecialchars($cancha_id); ?>">
            <input type="hidden" name="offset" value="<?php echo $offset - 10; ?>">
            <input type="hidden" name="duracion" value="<?php echo htmlspecialchars($duracion); ?>">
            <button type="submit" <?php echo ($offset <= 0) ? 'disabled' : ''; ?>>Retroceder</button>
        </form>

        <!-- Botones de fechas disponibles -->
        <?php foreach ($fechas_disponibles as $fecha_disponible): ?>
            <form action="" method="POST" style="display: inline;">
                <input type="hidden" name="cancha_id" value="<?php echo htmlspecialchars($cancha_id); ?>">
                <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($fecha_disponible); ?>">
                <input type="hidden" name="duracion" value="<?php echo htmlspecialchars($duracion); ?>">
                <input type="hidden" name="offset" value="<?php echo $offset; ?>">
                <button type="submit" class="fecha-boton <?php echo $fecha_disponible === $fecha ? 'seleccionado' : ''; ?>"><?php echo date('d-m', strtotime($fecha_disponible)); ?></button>
            </form>
        <?php endforeach; ?>

        <!-- Botón de avanzar -->
        <form action="" method="POST" style="display: inline;">
            <input type="hidden" name="cancha_id" value="<?php echo htmlspecialchars($cancha_id); ?>">
            <input type="hidden" name="offset" value="<?php echo $offset + 10; ?>">
            <input type="hidden" name="duracion" value="<?php echo htmlspecialchars($duracion); ?>">
            <button type="submit" <?php echo ($offset >= $max_offset - 1) ? 'disabled' : ''; ?>>Avanzar</button>
        </form>
    </div>

    <?php if ($fecha && $cancha_id): ?>
    <form action="../controlador/controlador.php?action=reservar" method="POST">
        <div class="bloque-horas">
            <h3>Mañana (07:00 - 12:00)</h3>
            <div>
                <?php
                $hora_actual = strtotime($hora_inicio);
                while ($hora_actual < strtotime('12:00')) {
                    $hora_formato = date('H:i', $hora_actual);
                    $disabled = isset($horarios_ocupados[$hora_formato]) ? 'disabled' : '';

                    echo "<button type='submit' name='hora_inicio' value='{$hora_formato}' class='hora-boton " . ($disabled ? "ocupado" : "reservar") . "' " . ($disabled ? "disabled" : "") . ">" .
                        "{$hora_formato}" .
                        "</button>";
                    echo "<input type='hidden' name='fecha' value='{$fecha}'>";
                    echo "<input type='hidden' name='duracion' value='{$duracion}'>";
                    echo "<input type='hidden' name='cancha_id' value='{$cancha_id}'>";
                    $hora_actual = strtotime("+{$intervalo} minutes", $hora_actual);
                }
                ?>
            </div>
        </div>

        <div class="bloque-horas">
            <h3>Tarde (12:00 - 18:00)</h3>
            <div>
                <?php
                $hora_actual = strtotime('12:00');
                while ($hora_actual < strtotime('18:00')) {
                    $hora_formato = date('H:i', $hora_actual);
                    $disabled = isset($horarios_ocupados[$hora_formato]) ? 'disabled' : '';

                    echo "<button type='submit' name='hora_inicio' value='{$hora_formato}' class='hora-boton " . ($disabled ? "ocupado" : "reservar") . "' " . ($disabled ? "disabled" : "") . ">" .
                        "{$hora_formato}" .
                        "</button>";
                    echo "<input type='hidden' name='fecha' value='{$fecha}'>";
                    echo "<input type='hidden' name='duracion' value='{$duracion}'>";
                    echo "<input type='hidden' name='cancha_id' value='{$cancha_id}'>";
                    $hora_actual = strtotime("+{$intervalo} minutes", $hora_actual);
                }
                ?>
            </div>
        </div>

        <div class="bloque-horas">
            <h3>Noche (18:00 - 22:00)</h3>
            <div>
                <?php
                $hora_actual = strtotime('18:00');
                while ($hora_actual < strtotime($hora_fin)) {
                    $hora_formato = date('H:i', $hora_actual);
                    $disabled = isset($horarios_ocupados[$hora_formato]) ? 'disabled' : '';

                    echo "<button type='submit' name='hora_inicio' value='{$hora_formato}' class='hora-boton " . ($disabled ? "ocupado" : "reservar") . "' " . ($disabled ? "disabled" : "") . ">" .
                        "{$hora_formato}" .
                        "</button>";
                    echo "<input type='hidden' name='fecha' value='{$fecha}'>";
                    echo "<input type='hidden' name='duracion' value='{$duracion}'>";
                    echo "<input type='hidden' name='cancha_id' value='{$cancha_id}'>";
                    $hora_actual = strtotime("+{$intervalo} minutes", $hora_actual);
                }
                ?>
            </div>
        </div>
    </form>
    <?php endif; ?>
</body>
</html>