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
$jornadas = [
    'mañana' => [
        "07:00", "07:30", "08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30"
    ],
    'tarde' => [
        "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30"
    ],
    'noche' => [
        "17:00", "17:30", "18:00", "18:30", "19:00", "19:30", "20:00", "20:30", "21:00", "21:30", "22:00"
    ]
];

$selected_hours = []; // Arreglo para las horas seleccionadas

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

        .resaltado {
            background-color: #FFC107; /* Color amarillo para resaltado */
        }
    </style>
    <script>
        function resaltarHora(boton) {
            const horas = document.querySelectorAll('.hora-boton');
            horas.forEach(hora => {
                hora.classList.remove('resaltado');
            });

            // Resaltar la hora seleccionada y las siguientes según la duración
            let duracion = parseInt(boton.getAttribute('data-duracion'));
            let inicio = boton.value;
            let tiempo = inicio.split(':');
            let horaInicio = new Date(0, 0, 0, parseInt(tiempo[0]), parseInt(tiempo[1]));

            for (let i = 0; i < duracion / 30; i++) {
                let horaResaltada = new Date(horaInicio.getTime() + (i * 30 * 60000)); // Incrementar 30 minutos
                let horaResaltadaStr = horaResaltada.toTimeString().substr(0, 5); // Formato HH:mm
                const botonResaltado = document.querySelector(`button[value='${horaResaltadaStr}']`);
                if (botonResaltado) {
                    botonResaltado.classList.add('resaltado');
                }
            }
        }
    </script>
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
                <input type="hidden" name="offset" value="<?php echo htmlspecialchars($offset); ?>">
                <button type="submit" class="fecha-boton <?php echo ($fecha_disponible == $fecha) ? 'seleccionado' : ''; ?>">
                    <?php echo htmlspecialchars($fecha_disponible); ?>
                </button>
            </form>
        <?php endforeach; ?>

        <!-- Botón de avanzar -->
        <form action="" method="POST" style="display: inline;">
            <input type="hidden" name="cancha_id" value="<?php echo htmlspecialchars($cancha_id); ?>">
            <input type="hidden" name="offset" value="<?php echo $offset + 10; ?>">
            <input type="hidden" name="duracion" value="<?php echo htmlspecialchars($duracion); ?>">
            <button type="submit" <?php echo ($offset >= $max_offset) ? 'disabled' : ''; ?>>Avanzar</button>
        </form>
    </div>

    <h2>Horarios disponibles para el <?php echo htmlspecialchars($fecha); ?></h2>
    
    <?php foreach ($jornadas as $jornada => $horas): ?>
    <div class="bloque-horas">
        <h3><?php echo ucfirst($jornada); ?></h3>
        <?php foreach ($horas as $hora): ?>
            <?php
            // Ocultar bloques según la duración seleccionada
            if ($duracion == 60 && in_array($hora, ["21:30", "22:00"])) continue;
            if ($duracion == 90 && in_array($hora, ["21:00", "21:30", "22:00"])) continue;
            if ($duracion == 120 && in_array($hora, ["20:30", "21:00", "21:30", "22:00"])) continue;
            ?>
            <form action="" method="POST" style="display: inline;">
                <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>">
                <input type="hidden" name="cancha_id" value="<?php echo htmlspecialchars($cancha_id); ?>">
                <input type="hidden" name="duracion" value="<?php echo htmlspecialchars($duracion); ?>">
                <button type="button" 
                        class="hora-boton <?php echo isset($horarios_ocupados[$hora]) ? 'ocupado' : ''; ?>" 
                        value="<?php echo htmlspecialchars($hora); ?>" 
                        data-duracion="<?php echo $duracion; ?>" 
                        onclick="resaltarHora(this)" 
                        <?php echo isset($horarios_ocupados[$hora]) ? 'disabled' : ''; ?>>
                    <?php echo htmlspecialchars($hora); ?>
                </button>
            </form>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

    <h2>Reserva tu horario:</h2>
    <form id="reserva-form" action="../controlador/controlador.php?action=reservar" method="POST">
        <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>">
        <input type="hidden" name="cancha_id" value="<?php echo htmlspecialchars($cancha_id); ?>">
        <input type="hidden" name="duracion" value="<?php echo htmlspecialchars($duracion); ?>">
        <input type="hidden" name="hora_inicio" id="hora_inicio" value="">
        <button type="submit" class="reservar" disabled id="boton_reservar">Reservar</button>
    </form>

    <script>
        const horas = document.querySelectorAll('.hora-boton');
        horas.forEach(hora => {
            hora.addEventListener('click', function() {
                // Establecer el valor en el campo oculto
                document.getElementById('hora_inicio').value = this.value;

                // Activar el botón de reservar
                document.getElementById('boton_reservar').disabled = false;
            });
        });
    </script>
</body>
</html>
